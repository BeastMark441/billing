<template>
    <div v-if="server" class="space-y-8">
        <!-- Header -->
        <div class="glass-card p-6 rounded-2xl flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ server.name }}</h1>
                <div class="flex items-center gap-4 text-sm text-gray-400">
                    <span>IP: <span class="text-white">{{ server.ip }}:{{ server.port }}</span></span>
                    <span>Node: <span class="text-white">{{ server.node?.name }}</span></span>
                    <span :class="{'text-green-400': server.status === 'active', 'text-red-400': server.status !== 'active'}">
                        {{ server.status.toUpperCase() }}
                    </span>
                </div>
            </div>
            <div class="flex gap-4">
                <button @click="sendPower('start')" class="bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-lg font-bold transition shadow-lg shadow-green-900/20">
                    Start
                </button>
                <button @click="sendPower('restart')" class="bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 rounded-lg font-bold transition shadow-lg shadow-yellow-900/20">
                    Restart
                </button>
                <button @click="sendPower('stop')" class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg font-bold transition shadow-lg shadow-red-900/20">
                    Stop
                </button>
                <button @click="sendPower('kill')" class="bg-red-900 hover:bg-red-800 text-white px-4 py-2 rounded-lg font-bold transition">
                    Kill
                </button>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="glass-card p-6 rounded-2xl">
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <span class="w-3 h-3 bg-blue-500 rounded-full"></span> CPU Usage
                </h3>
                <div class="h-64 flex items-end justify-between gap-1">
                     <!-- Simple CSS Bar Chart for MVP visualization -->
                     <div v-for="(val, i) in cpuHistory" :key="i" 
                          class="bg-blue-500/50 w-full rounded-t transition-all duration-300 hover:bg-blue-400"
                          :style="{ height: val + '%' }">
                     </div>
                </div>
                <div class="text-right mt-2 font-mono text-blue-400">{{ currentCpu }}%</div>
            </div>
            <div class="glass-card p-6 rounded-2xl">
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <span class="w-3 h-3 bg-purple-500 rounded-full"></span> RAM Usage
                </h3>
                <div class="h-64 flex items-end justify-between gap-1">
                     <div v-for="(val, i) in ramHistory" :key="i" 
                          class="bg-purple-500/50 w-full rounded-t transition-all duration-300 hover:bg-purple-400"
                          :style="{ height: (val / maxRam * 100) + '%' }">
                     </div>
                </div>
                <div class="text-right mt-2 font-mono text-purple-400">{{ currentRam }} MB / {{ maxRam }} MB</div>
            </div>
        </div>

        <!-- Console / Status -->
        <div class="glass-card p-6 rounded-2xl">
             <h3 class="text-xl font-bold mb-4">Server Status</h3>
             <div class="bg-black/50 p-4 rounded-lg font-mono text-sm h-48 overflow-y-auto text-gray-300">
                 <div v-if="!resources">Connecting to server...</div>
                 <div v-else>
                     State: <span class="text-primary">{{ resources.current_state }}</span><br>
                     Uptime: {{ Math.floor(resources.resources.uptime / 1000) }}s<br>
                     Disk: {{ (resources.resources.disk_bytes / 1024 / 1024).toFixed(2) }} MB<br>
                     Network RX: {{ (resources.resources.network_rx_bytes / 1024 / 1024).toFixed(2) }} MB<br>
                     Network TX: {{ (resources.resources.network_tx_bytes / 1024 / 1024).toFixed(2) }} MB
                 </div>
             </div>
        </div>
    </div>
    <div v-else class="text-center py-20 text-gray-500">
        Loading server details...
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            server: null,
            resources: null,
            cpuHistory: Array(20).fill(0),
            ramHistory: Array(20).fill(0),
            maxRam: 1024, // Default, will update from product
            timer: null
        }
    },
    computed: {
        currentCpu() {
            return this.resources?.resources?.cpu_absolute.toFixed(1) || 0;
        },
        currentRam() {
             return this.resources ? (this.resources.resources.memory_bytes / 1024 / 1024).toFixed(0) : 0;
        }
    },
    async mounted() {
        await this.fetchServer();
        this.startPolling();
    },
    beforeUnmount() {
        clearInterval(this.timer);
    },
    methods: {
        async fetchServer() {
            try {
                const response = await axios.get(`/client/servers/${this.$route.params.id}`);
                this.server = response.data;
                this.maxRam = this.server.product?.resources?.ram || 1024;
            } catch (error) {
                console.error('Failed to load server', error);
            }
        },
        async startPolling() {
            this.timer = setInterval(async () => {
                if (!this.server) return;
                try {
                    const res = await axios.get(`/client/servers/${this.server.id}/resources`);
                    this.resources = res.data;
                    
                    if (this.resources.resources) {
                        this.cpuHistory.push(this.resources.resources.cpu_absolute);
                        this.cpuHistory.shift();
                        
                        this.ramHistory.push(this.resources.resources.memory_bytes / 1024 / 1024);
                        this.ramHistory.shift();
                    }
                } catch (error) {
                    console.error('Stats poll failed', error);
                }
            }, 3000); // Poll every 3s
        },
        async sendPower(signal) {
            if (!confirm(`Are you sure you want to ${signal} the server?`)) return;
            try {
                await axios.post(`/client/servers/${this.server.id}/power`, { signal });
                alert(`Signal ${signal} sent!`);
            } catch (error) {
                alert('Power action failed: ' + error.response?.data?.error);
            }
        }
    }
}
</script>
