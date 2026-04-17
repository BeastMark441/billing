<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProxmoxVeService
{
    protected string $baseUrl;
    protected string $tokenId;
    protected string $tokenSecret;
    protected bool $verifySsl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.proxmoxve.url'), '/');
        $this->tokenId = (string) config('services.proxmoxve.token_id');
        $this->tokenSecret = (string) config('services.proxmoxve.token_secret');
        $this->verifySsl = (bool) config('services.proxmoxve.verify_ssl', true);
    }

    protected function request()
    {
        if ($this->baseUrl === '' || $this->tokenId === '' || $this->tokenSecret === '') {
            throw new Exception('ProxmoxVE не настроен. Проверьте переменные окружения PROXMOX_URL, PROXMOX_TOKEN_ID, PROXMOX_TOKEN_SECRET.');
        }

        $req = Http::withHeaders([
            'Authorization' => 'PVEAPIToken='.$this->tokenId.'='.$this->tokenSecret,
        ])->acceptJson();

        if (! $this->verifySsl) {
            $req = $req->withoutVerifying();
        }

        return $req;
    }

    protected function apiGetData(string $path, array $query = []): mixed
    {
        $response = $this->request()->get($this->baseUrl.'/api2/json'.$path, $query);
        if (! $response->successful()) {
            throw new Exception('ProxmoxVE API error: '.$response->body());
        }

        return $response->json()['data'] ?? null;
    }

    protected function apiPostData(string $path, array $data = []): mixed
    {
        $response = $this->request()->asForm()->post($this->baseUrl.'/api2/json'.$path, $data);
        if (! $response->successful()) {
            throw new Exception('ProxmoxVE API error: '.$response->body());
        }

        return $response->json()['data'] ?? null;
    }

    public function provisionServer(Order $order): void
    {
        $service = $order->service;
        $specs = $service?->specifications ?? [];
        $proxmox = $specs['proxmox'] ?? $specs;

        $node = (string) ($proxmox['node'] ?? '');
        $type = (string) ($proxmox['type'] ?? '');
        $templateVmid = (int) ($proxmox['template_vmid'] ?? 0);

        if ($node === '' || ! in_array($type, ['lxc', 'qemu'], true) || $templateVmid <= 0) {
            throw new Exception('Некорректные настройки ProxmoxVE в тарифе. Требуются: node, type (lxc/qemu), template_vmid.');
        }

        $nextIdData = $this->apiGetData('/cluster/nextid');
        $newVmid = 0;
        if (is_array($nextIdData) && isset($nextIdData['nextid'])) {
            $newVmid = (int) $nextIdData['nextid'];
        } elseif (is_numeric($nextIdData)) {
            $newVmid = (int) $nextIdData;
        }
        if ($newVmid <= 0) {
            throw new Exception('Не удалось получить next VMID из Proxmox.');
        }

        $name = Str::limit(Str::slug(($service?->name ?? 'vps').'-'.$order->id, '-'), 30, '');
        $clonePayload = [
            'newid' => $newVmid,
            'name' => $name.'-'.$newVmid,
            'full' => 1,
        ];

        if (! empty($proxmox['storage'])) {
            $clonePayload['storage'] = (string) $proxmox['storage'];
        }

        $clonePath = $type === 'lxc'
            ? "/nodes/{$node}/lxc/{$templateVmid}/clone"
            : "/nodes/{$node}/qemu/{$templateVmid}/clone";

        $this->apiPostData($clonePath, $clonePayload);

        $config = [];
        if (! empty($proxmox['cores'])) {
            $config['cores'] = (int) $proxmox['cores'];
        }
        if (! empty($proxmox['memory_mb'])) {
            $config['memory'] = (int) $proxmox['memory_mb'];
        }

        if (! empty($config)) {
            $configPath = $type === 'lxc'
                ? "/nodes/{$node}/lxc/{$newVmid}/config"
                : "/nodes/{$node}/qemu/{$newVmid}/config";
            $this->apiPostData($configPath, $config);
        }

        $startPath = $type === 'lxc'
            ? "/nodes/{$node}/lxc/{$newVmid}/status/start"
            : "/nodes/{$node}/qemu/{$newVmid}/status/start";
        $this->apiPostData($startPath);

        $order->update([
            'status' => 'active',
            'proxmox_node' => $node,
            'proxmox_vmid' => $newVmid,
            'proxmox_type' => $type,
        ]);
    }
}
