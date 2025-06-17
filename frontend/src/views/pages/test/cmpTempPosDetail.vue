<template>
  <div id="page-content" v-if="isLogin == 1" style="min-height: 850px">
    <div class="block">
      <div class="block-title">
        <h2>
          <strong>POS DETAIL</strong>
        </h2>
        <i v-if="!status_table" class="fa fa-spinner fa-spin text-default"></i>
      </div>

      <div class="block-content">
        <div id="wrapper2"></div>
        <div id="box"></div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { Grid, h } from "gridjs";
import "gridjs/dist/theme/mermaid.css";

export default {
  data() {
    return {
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      grid: new Grid(),
      status_table: false,
    };
  },

  mounted() {
    this.getTable();
  },

  methods: {
    async handleDelete(profileId) {
      // Konfirmasi penghapusan dengan SweetAlert2
      const result = await Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
      });

      if (result.isConfirmed) {
        try {
          // Encode profile_id untuk handle spasi dan karakter khusus
          const encodedProfileId = encodeURIComponent(profileId);

          const response = await axios.delete(
            `${this.$root.apiHost}api/temp-pos-detail/delete/${encodedProfileId}`,
            {
              headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
              },
            }
          );

          if (response.data.status) {
            // Notifikasi sukses
            await Swal.fire({
              title: "Berhasil!",
              text: "Data telah berhasil dihapus",
              icon: "success",
              timer: 2000,
              showConfirmButton: false,
            });
            this.refreshTable();
          } else {
            // Notifikasi error dengan pesan dari server
            await Swal.fire({
              title: "Gagal!",
              text: response.data.message || "Gagal menghapus data",
              icon: "error",
            });
          }
        } catch (error) {
          // Notifikasi error
          await Swal.fire({
            title: "Error!",
            text:
              error.response?.data?.message ||
              "Terjadi kesalahan saat menghapus data",
            icon: "error",
          });
          console.error("Error:", error);
        }
      }
    },
    refreshTable() {
      this.status_table = false;
      $("#wrapper2").remove();
      const e = $('<div id="wrapper2"></div>');
      $("#box").append(e);
      this.getTable();
    },

    getTable() {
      this.grid = new Grid();
      this.grid.updateConfig({
        pagination: {
          limit: this.$root.pagingTabel1,
          server: {
            url: (prev, page, limit) =>
              `${prev}${prev.includes("?") ? "&" : "?"}limit=${limit}&offset=${
                page * limit
              }`,
          },
        },
        search: {
          server: {
            url: (prev, keyword) => `${prev}?search=${keyword}`,
          },
        },
        columns: [
          "No",
          "Cabang",
          "Nama BA",
          {
            name: "PK Final",
            formatter: (cell) => `${cell}`,
          },
          {
            name: "Achievement Siba Final",
            formatter: (cell) => `${cell}%`,
          },
          {
            name: "Achievement Soba Final",
            formatter: (cell) => `${cell}%`,
          },
          {
            name: "Hasil Perhitungan",
            formatter: (cell) => {
              if (!cell || cell === "N/A") return "0%";
              const num = parseFloat(cell);
              return `${
                num === Math.floor(num) ? num.toFixed(0) : num.toFixed(2)
              }%`;
            },
          },
          {
            name: "Bobot yang Digunakan",
            formatter: (cell) => cell,
          },
          {
            name: "Kriteria BB/TB",
            formatter: (cell) => {
              const kriteria = cell.split("(")[0].trim();
              let buttonClass = "";
              switch (kriteria) {
                case "Normal":
                  buttonClass = "btn-success";
                  break;
                case "Kurus":
                  buttonClass = "btn-warning";
                  break;
                case "Gemuk":
                  buttonClass = "btn-warning";
                  break;
                case "Obesitas":
                  buttonClass = "btn-danger";
                  break;
                default:
                  buttonClass = "btn-secondary";
              }
              return h(
                "button",
                {
                  className: `btn btn-sm ${buttonClass}`,
                  style: {
                    minWidth: "100px",
                    padding: "5px 10px",
                    border: "none",
                    borderRadius: "4px",
                    cursor: "default",
                  },
                },
                cell || "N/A"
              );
            },
          },
          {
            name: "Result",
            formatter: (cell, row) => {
              let buttonClass;
              let isClickable = false;
              let profileId = ""; // Akan diisi dari data row

              // Dapatkan profileId dari data row
              if (row && row.cells) {
                const profileIdCell = row.cells.find(
                  (cell) => cell.data === row.cells[2].data
                );
                if (profileIdCell) {
                  profileId = profileIdCell.data;
                }
              }

              switch (cell) {
                case "Sudah di TakeOut":
                  buttonClass = "btn-danger";
                  isClickable = true;
                  break;
                case "Diperpanjang":
                  buttonClass = "btn-success";
                  break;
                case "Dipertimbangkan":
                  buttonClass = "btn-warning";
                  break;
                default:
                  buttonClass = "btn-danger";
              }

              return h(
                "button",
                {
                  className: `btn btn-sm ${buttonClass}`,
                  style: {
                    minWidth: "100px",
                    padding: "5px 10px",
                    border: "none",
                    borderRadius: "4px",
                    cursor: isClickable ? "pointer" : "default",
                  },
                  onClick: isClickable
                    ? () => this.handleDelete(profileId)
                    : undefined,
                },
                cell
              );
            },
          },
        ],
        style: {
          table: {
            border: "1px solid #ccc",
            width: "auto",
            "min-width": "100%",
          },
          th: {
            "background-color": "rgba(0, 55, 255, 0.1)",
            color: "#000",
            "border-bottom": "1px solid #ccc",
            "text-align": "center",
          },
          td: {
            "text-align": "center",
            "vertical-align": "middle",
            padding: "8px",
          },
        },
        server: {
          url: this.$root.apiHost + "api/temp-pos-detail",
          then: (data) => {
            console.log("Received Data:", data);
            return data.results.map((item, index) => {
              console.log("Processing Item:", item);
              return [
                data.nomorBaris + index,
                item.cabang_id || "-",
                item.profiles_id || "-",
                item.pk || "0",
                item.achv_siba || "0",
                item.achv_soba || "0",
                item.avg_result || "0",
                item.bobot_info || "Default (40:60)",
                item.kriteria || "N/A",
                item.result || "N/A",
              ];
            });
          },
          total: (data) => data.count,
        },
      });

      this.grid.render(document.getElementById("wrapper2"));
      this.status_table = true;
    },
  },
};
</script>

<style scoped>
.btn-success {
  background-color: #28a745 !important;
  color: white !important;
}

.btn-warning {
  background-color: #ffc107 !important;
  color: black !important;
}

.btn-danger {
  background-color: #dc3545 !important;
  color: white !important;
}

.btn-success:hover,
.btn-warning:hover,
.btn-danger:hover {
  opacity: 0.9;
}

.btn:focus {
  box-shadow: none !important;
}

.btn[style*="cursor: pointer"]:hover {
  opacity: 0.8;
  transform: scale(0.98);
}
</style>
