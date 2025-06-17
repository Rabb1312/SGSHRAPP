<template>
  <div id="page-content" v-if="isLogin == 1">
    <!-- Loading Indicator -->
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner">
        <i class="fa fa-spinner fa-spin"></i>
        <p>Memuat data ...</p>
      </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="stats-container">
      <div class="stat-card primary">
        <div class="stat-content">
          <div class="stat-header">
            <h4>Kontrak Berakhir Bulan Ini</h4>
          </div>
          <div class="stat-value">
            {{ kontrakBulanIni }}
          </div>
        </div>
      </div>

      <div class="stat-card success">
        <div class="stat-content">
          <div class="stat-header">
            <h4>Status Diperpanjang</h4>
          </div>
          <div class="stat-value">
            {{ statusDiperpanjang }}
          </div>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-content">
          <div class="stat-header">
            <h4>Status Dipertimbangkan</h4>
          </div>
          <div class="stat-value">
            {{ statusDipertimbangkan }}
          </div>
        </div>
      </div>

      <div class="stat-card danger">
        <div class="stat-content">
          <div class="stat-header">
            <h4>Status Tidak Diperpanjang</h4>
          </div>
          <div class="stat-value">
            {{ statusTidakDiperpanjang }}
          </div>
        </div>
      </div>
    </div>

    <!-- Overview Block -->
    <div class="overview-block">
      <div class="block-header">
        <h2>Overview Kontrak yang Akan Berakhir</h2>
        <button @click="checkKontrakBerakhir" class="btn btn-primary" :disabled="loading">
          <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-refresh'"></i> 
          {{ loading ? 'Loading...' : 'Refresh Data' }}
        </button>
      </div>

      <div class="contracts-grid">
        <div 
          v-for="kontrak in kontrakBerakhir" 
          :key="kontrak.id"
          class="contract-card"
          :class="{
            'bg-danger-light': kontrak.result === 'Tidak Diperpanjang',
            'bg-warning-light': kontrak.result === 'Dipertimbangkan',
            'bg-success-light': kontrak.result === 'Diperpanjang',
            'bg-secondary-light': !kontrak.result || kontrak.result === 'Belum ada status'
          }"
        >
          <div class="card-header">
            <span class="badge badge-primary">{{ kontrak.jabatan_id }}</span>
            <span class="badge" :class="getResultClass(kontrak.result)">
              {{ kontrak.result || "Belum ada status" }}
            </span>
          </div>

          <div class="card-body">
            <h5 class="card-title">{{ kontrak.no_kontrak }}</h5>
            
            <div class="info-grid">
              <div class="info-item">
                <i class="fa fa-user text-primary"></i>
                <strong>{{ kontrak.profiles_id }}</strong>
              </div>

              <div class="info-item">
                <i class="fa fa-building text-primary"></i>
                {{ kontrak.cabang_id }}
              </div>

              <div class="info-item">
                <i class="fa fa-file-text text-primary"></i>
                {{ kontrak.tipe }}
              </div>

              <div class="info-item">
                <i class="fa fa-calendar text-danger"></i>
                <strong>Berakhir: {{ formatTanggal(kontrak.tgl_keluar) }}</strong>
              </div>
            </div>

            <div 
              v-if="['Dipertimbangkan', 'Diperpanjang'].includes(kontrak.result)"
              class="card-actions"
            >
              <!-- <button class="btn btn-success" @click="prepareNewContract(kontrak)">
                <i class="fa fa-plus-circle"></i>
                Perpanjang Kontrak
              </button> -->
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && kontrakBerakhir.length === 0" class="empty-state">
        <i class="fa fa-calendar-check"></i>
        <h3>Tidak ada kontrak yang akan berakhir</h3>
        <p>Semua kontrak masih dalam periode aktif.</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

export default {
  data() {
    return {
      isLogin: localStorage.getItem('token') != null ? 1 : 0,
      kontrakBerakhir: [],
      kontrakBulanIni: 0,
      statusDiperpanjang: 0,
      statusDipertimbangkan: 0,
      statusTidakDiperpanjang: 0,
      loading: false,
      // Cache untuk mengurangi request berulang
      cachedProfiles: new Map(),
      cachedResults: new Map(),
      lastCacheTime: null,
      cacheExpiry: 5 * 60 * 1000 // 5 menit
    }
  },

  async mounted() {
    await this.checkKontrakBerakhir();
  },

  methods: {
    async checkKontrakBerakhir() {
      this.loading = true;
      
      try {
        console.time('checkKontrakBerakhir'); // Debug timing
        
        // Step 1: Paralel request untuk data utama
        const [expiringResponse, profilesResponse] = await Promise.all([
          axios.get(this.$root.apiHost + 'api/check-expiring'),
          this.getCachedProfiles()
        ]);
        
        if (!expiringResponse.data?.length) {
          this.kontrakBerakhir = [];
          this.calculateStats();
          return;
        }

        console.log(`Fetched ${expiringResponse.data.length} expiring contracts`);
        
        // Step 2: Cache profiles data untuk lookup cepat
        const activeProfiles = new Map();
        profilesResponse.forEach(profile => {
          if (profile.is_active === 'Aktif') {
            activeProfiles.set(profile.profile_name, profile);
          }
        });

        // Step 3: Filter kontrak yang memiliki profile aktif
        const validKontraks = expiringResponse.data.filter(kontrak => 
          activeProfiles.has(kontrak.profiles_id)
        );

        console.log(`Filtered to ${validKontraks.length} valid contracts`);

        if (validKontraks.length === 0) {
          this.kontrakBerakhir = [];
          this.calculateStats();
          return;
        }
        
        // Step 4: Batch request untuk lastExitDate
        const lastExitPromises = validKontraks.map(kontrak =>
          axios.get(this.$root.apiHost + `api/lastExitDate?profiles_id=${kontrak.profiles_id}`)
            .then(response => ({
              profiles_id: kontrak.profiles_id,
              tgl_keluar: response.data.tgl_keluar
            }))
            .catch(error => {
              console.error(`Error fetching lastExitDate for ${kontrak.profiles_id}:`, error);
              return {
                profiles_id: kontrak.profiles_id,
                tgl_keluar: null
              };
            })
        );
        
        const lastExitResults = await Promise.all(lastExitPromises);
        const lastExitMap = new Map(
          lastExitResults.map(item => [item.profiles_id, item.tgl_keluar])
        );
        
        // Step 5: Filter kontrak berdasarkan lastExitDate
        const finalValidKontraks = validKontraks.filter(kontrak => 
          lastExitMap.get(kontrak.profiles_id) === kontrak.tgl_keluar
        );

        console.log(`Final valid contracts: ${finalValidKontraks.length}`);

        if (finalValidKontraks.length === 0) {
          this.kontrakBerakhir = [];
          this.calculateStats();
          return;
        }
        
        // Step 6: Batch request untuk temp-pos-detail results
        const resultPromises = finalValidKontraks.map(kontrak =>
          this.getCachedResult(kontrak.profiles_id)
        );
        
        const resultData = await Promise.all(resultPromises);
        const resultMap = new Map(
          resultData.map(item => [item.profiles_id, item.result])
        );
        
        // Step 7: Combine semua data
        this.kontrakBerakhir = finalValidKontraks.map(kontrak => ({
          ...kontrak,
          result: resultMap.get(kontrak.profiles_id) || "Belum ada status",
          resultClass: this.getResultClass(resultMap.get(kontrak.profiles_id))
        }));
        
        this.calculateStats();
        
        console.timeEnd('checkKontrakBerakhir'); // Debug timing
        
        toast.success(`Berhasil memuat ${this.kontrakBerakhir.length} kontrak`, { 
          theme: "colored",
          autoClose: 2000
        });
        
      } catch (error) {
        console.error("Error in checkKontrakBerakhir:", error);
        toast.error("Gagal memuat data kontrak: " + (error.message || 'Unknown error'), { 
          theme: "colored" 
        });
        this.kontrakBerakhir = [];
      } finally {
        this.loading = false;
      }
    },

    // Cache method untuk profiles data
    async getCachedProfiles() {
      const now = Date.now();
      
      // Check cache validity
      if (this.cachedProfiles.size > 0 && 
          this.lastCacheTime && 
          (now - this.lastCacheTime) < this.cacheExpiry) {
        console.log('Using cached profiles data');
        return Array.from(this.cachedProfiles.values());
      }
      
      // Fetch fresh data
      console.log('Fetching fresh profiles data');
      const response = await axios.get(this.$root.apiHost + 'api/profilesalldata');
      
      // Update cache
      this.cachedProfiles.clear();
      response.data.forEach(profile => {
        this.cachedProfiles.set(profile.profile_name, profile);
      });
      this.lastCacheTime = now;
      
      return response.data;
    },

    // Cache method untuk result data
    async getCachedResult(profileId) {
      // Check if result is already cached
      if (this.cachedResults.has(profileId)) {
        return this.cachedResults.get(profileId);
      }
      
      try {
        const response = await axios.get(this.$root.apiHost + 'api/temp-pos-detail', {
          params: { profiles_id: profileId }
        });
        
        const resultData = response.data.results?.find(item => 
          item.profiles_id === profileId
        );
        
        const result = {
          profiles_id: profileId,
          result: resultData?.result || "Belum ada status"
        };
        
        // Cache the result
        this.cachedResults.set(profileId, result);
        
        return result;
        
      } catch (error) {
        console.error(`Error fetching result for ${profileId}:`, error);
        const errorResult = {
          profiles_id: profileId,
          result: "Belum ada status"
        };
        
        this.cachedResults.set(profileId, errorResult);
        return errorResult;
      }
    },

    calculateStats() {
      this.kontrakBulanIni = this.kontrakBerakhir.length;
      this.statusDiperpanjang = this.kontrakBerakhir.filter(k => k.result === 'Diperpanjang').length;
      this.statusDipertimbangkan = this.kontrakBerakhir.filter(k => k.result === 'Dipertimbangkan').length;
      this.statusTidakDiperpanjang = this.kontrakBerakhir.filter(k => k.result === 'Tidak Diperpanjang').length;
    },

    getResultClass(result) {
      switch (result) {
        case "Diperpanjang": return "badge-success";
        case "Dipertimbangkan": return "badge-warning";
        case "Tidak Diperpanjang": return "badge-danger";
        default: return "badge-secondary";
      }
    },

    formatTanggal(tanggal) {
      if (!tanggal) return '-';
      return new Date(tanggal).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
    },

    async prepareNewContract(kontrak) {
      try {
        // Implementasi sesuai kebutuhan
        console.log("Preparing new contract for:", kontrak);
        toast.info(`Mempersiapkan kontrak baru untuk ${kontrak.profiles_id}`, {
          theme: "colored"
        });
        
        // Add your contract preparation logic here
        
      } catch (error) {
        console.error("Error preparing contract:", error);
        toast.error("Gagal mempersiapkan kontrak baru", { theme: "colored" });
      }
    },

    // Method untuk clear cache jika diperlukan
    clearCache() {
      this.cachedProfiles.clear();
      this.cachedResults.clear();
      this.lastCacheTime = null;
      console.log('Cache cleared');
    }
  },

  // Cleanup ketika component destroyed
  beforeUnmount() {
    this.clearCache();
  }
}
</script>

<style scoped>
/* Global Styles */
* {
  box-sizing: border-box;
  scroll-behavior: smooth;
}

/* Loading Overlay */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.loading-spinner {
  background: white;
  padding: 30px;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.loading-spinner i {
  font-size: 2rem;
  color: #007bff;
  margin-bottom: 15px;
}

.loading-spinner p {
  margin: 0;
  color: #333;
  font-weight: 500;
}

/* Main Container */
#page-content {
  min-height: 100vh;
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
  overflow-x: auto;
}

/* Stats Container - Flexbox untuk responsivitas */
.stats-container {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin-bottom: 30px;
  width: 100%;
}

.stat-card {
  flex: 1;
  min-width: 250px;
  border-radius: 10px;
  padding: 20px;
  color: white;
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-card.primary { background: #007bff; }
.stat-card.success { background: #28a745; }
.stat-card.warning { background: #ffc107; color: #000; }
.stat-card.danger { background: #dc3545; }

.stat-header h4 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 500;
}

.stat-value {
  font-size: 2.5rem;
  font-weight: bold;
  margin-top: 10px;
  line-height: 1.2;
}

/* Overview Block */
.overview-block {
  background: white;
  border-radius: 10px;
  margin: 0;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  width: 100%;
}

.block-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 15px;
}

.block-header h2 {
  margin: 0;
  font-size: 1.8rem;
  color: #333;
}

/* Contracts Grid */
.contracts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  width: 100%;
}

.contract-card {
  background: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
  width: 100%;
  min-height: 200px;
}

.contract-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Background Colors */
.bg-danger-light {
  background-color: #ffebee !important;
  border-left: 4px solid #dc3545;
}

.bg-warning-light {
  background-color: #fff3e0 !important;
  border-left: 4px solid #ffc107;
}

.bg-success-light {
  background-color: #e8f5e9 !important;
  border-left: 4px solid #28a745;
}

.bg-secondary-light {
  background-color: #f8f9fa !important;
  border-left: 4px solid #6c757d;
}

/* Card Components */
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  flex-wrap: wrap;
  gap: 10px;
}

.card-title {
  margin: 0 0 15px 0;
  font-size: 1.2rem;
  font-weight: 600;
  color: #333;
}

/* Badges */
.badge {
  padding: 8px 12px;
  border-radius: 5px;
  font-size: 0.85rem;
  font-weight: 600;
  white-space: nowrap;
}

.badge-primary { background: #007bff; color: white; }
.badge-success { background: #28a745; color: white; }
.badge-warning { background: #ffc107; color: black; }
.badge-danger { background: #dc3545; color: white; }
.badge-secondary { background: #6c757d; color: white; }

/* Info Grid */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 15px;
  margin: 15px 0;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.9rem;
  word-break: break-word;
}

.info-item i {
  flex-shrink: 0;
  width: 16px;
}

/* Buttons */
.card-actions {
  margin-top: 20px;
  text-align: right;
}

.btn {
  padding: 8px 16px;
  border-radius: 5px;
  border: none;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
  font-size: 0.9rem;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-success {
  background: #28a745;
  color: white;
}

.btn:hover:not(:disabled) {
  opacity: 0.9;
  transform: translateY(-2px);
}

.btn i {
  margin-right: 5px;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #6c757d;
}

.empty-state i {
  font-size: 4rem;
  margin-bottom: 20px;
  color: #dee2e6;
}

.empty-state h3 {
  margin: 0 0 10px 0;
  font-size: 1.5rem;
}

.empty-state p {
  margin: 0;
  font-size: 1rem;
}

/* Text Colors */
.text-primary { color: #007bff !important; }
.text-danger { color: #dc3545 !important; }

/* Responsive Design */

/* Large Desktop */
@media (min-width: 1200px) {
  .stats-container {
    gap: 25px;
  }
  
  .contracts-grid {
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
  }
}

/* Tablet Landscape */
@media (max-width: 1024px) {
  #page-content {
    padding: 15px;
  }
  
  .stats-container {
    gap: 15px;
  }
  
  .stat-card {
    min-width: 220px;
  }
  
  .contracts-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  }
  
  .block-header {
    flex-direction: column;
    align-items: stretch;
    text-align: center;
  }
  
  .block-header h2 {
    font-size: 1.6rem;
  }
}

/* Tablet Portrait */
@media (max-width: 768px) {
  #page-content {
    padding: 15px 10px;
  }
  
  .stats-container {
    flex-direction: column;
    gap: 15px;
  }
  
  .stat-card {
    min-width: auto;
    flex: none;
  }
  
  .stat-value {
    font-size: 2.2rem;
  }
  
  .contracts-grid {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }
  
  .card-header {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }
  
  .badge {
    text-align: center;
  }
}

/* Mobile */
@media (max-width: 480px) {
  #page-content {
    padding: 10px 5px;
  }
  
  .stat-card {
    padding: 15px;
  }
  
  .stat-value {
    font-size: 2rem;
  }
  
  .stat-header h4 {
    font-size: 1rem;
  }
  
  .contract-card {
    padding: 15px;
  }
  
  .block-header h2 {
    font-size: 1.4rem;
  }
  
  .btn {
    width: 100%;
    padding: 10px;
  }
  
  .card-actions {
    text-align: center;
  }
}

/* High DPI Displays */
@media (min-resolution: 144dpi) {
  .stat-value {
    font-size: 2.3rem;
  }
}

@media (min-resolution: 192dpi) {
  .stat-value {
    font-size: 2.1rem;
  }
  
  .btn {
    padding: 10px 18px;
  }
}

/* Print Styles */
@media print {
  .btn {
    display: none;
  }
  
  .contract-card {
    box-shadow: none;
    border: 1px solid #ddd;
  }
  
  .stat-card {
    color: black !important;
    background: white !important;
    border: 1px solid #ddd;
  }
  
  .loading-overlay {
    display: none;
  }
}
</style>