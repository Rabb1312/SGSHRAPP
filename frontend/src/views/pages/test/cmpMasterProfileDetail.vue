<template>
  <!---------------------------- Modal 5 Tahun -->
  <div
    :class="modalFiveTahun ? 'modal fade in' : 'modal fade'"
    id="alertLimaThModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="alertLimaThModalTitle"
    aria-hidden="true"
    data-keyboard="false"
    data-backdrop="static"
    :style="modalFiveTahun ? 'display: block;' : 'display: none;'"
  >
    <div class="modal-dialog2 modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Peringatan Masa Kerja 5 Tahun</h4>
          <button
            type="button"
            class="close"
            @click="modalFiveTahun = false"
            aria-label="Close"
          >
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div v-if="selectedEmployee">
            <div class="alert alert-warning">
              <span class="btn btn-sm btn-primary">
                {{ selectedEmployee.jabatan_id }}
              </span>
              <h5 class="mb-2">
                <b>Nama: {{ selectedEmployee.profiles_id }}</b>
              </h5>
              <p class="mb-1">Cabang: {{ selectedEmployee.cabang_id }}</p>
              <p class="mb-1">Unit: {{ selectedEmployee.unit_id }}</p>
              <p class="mb-0">
                <strong>
                  Masa Kerja:
                  {{ selectedEmployee.tahun }} Tahun
                  {{ selectedEmployee.bulan }} Bulan
                  {{ selectedEmployee.hari }} Hari
                </strong>
              </p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-default"
            @click="modalFiveTahun = false"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
  <!---------------------------- Modal -->
  <div
    v-if="modalTakeOut"
    :class="'modal fade in'"
    style="display: block; background-color: rgba(0, 0, 0, 0.5)"
  >
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Take Out</h5>
          <button type="button" class="close" @click="modalTakeOut = false">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <pre>{{ todo }}</pre>
          <div class="form-group">
            <label for="example-nf-email">Nama Karyawan</label>
            <v-select
              id="profiles_id"
              variant="outlined"
              label="profiles_id"
              :options="profiles"
              placeholder="Pilih Karyawan"
              v-model="todo.profiles_id"
              :reduce="(profile) => profile.profiles_id"
              @input="onChangeProfile"
            >
            </v-select>
          </div>
          <div class="form-group">
            <label for="tgl_take_out">Tanggal Take Out</label>
            <input
              type="date"
              v-model="todo.tgl_take_out"
              class="form-control"
            />
          </div>
          <div class="form-group">
            <label for="ket">Keterangan</label>
            <select v-model="todo.ket" class="form-control">
              <option disabled value="">Pilih Keterangan</option>
              <option value="Resign">Resign</option>
              <option value="Pensiun">Pensiun</option>
              <option value="Habis Kontrak">Habis Kontrak</option>
              <option value="Pindah Unit">Pindah Unit</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="modalTakeOut = false">
            Cancel
          </button>
          <button class="btn btn-primary" @click="saveTakeOut">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!---------------------------- Modal -->

  <!-- Page content -->
  <div id="page-content" v-if="isLogin == 1" style="min-height: 850px">
    <!-- END Visible Main Sidebar Header -->

    <!-- Block -->
    <div class="block">
      <!-- Block Title -->
      <div class="block-title">
        <h2>
          <strong>MENU {{ $root.judulHalaman }}</strong>
        </h2>

        <i v-if="!status_table" class="fa fa-spinner fa-spin text-default"></i>
      </div>
      <!-- END Block Title -->

      <div class="block-content">
        <!------------------------>
        <!-- Button trigger modal -->

        <download-excel
          class="button"
          :data="json_data"
          :fields="json_fields"
          :worksheet="nama_sheetnya"
          :name="nama_excelnya"
          :before-generate="startDownload"
          :before-finish="finishDownload"
          type="xlsx"
        >
          <button
            class="btn btn-sm btn-success pull-left"
            @click="download_excel_xyz()"
          >
            <i class="class fas fa-file-excel"></i>
            Export Excel
          </button>
        </download-excel>

        <!------------------------>
        <div id="wrapper2"></div>
        <div id="box"></div>
      </div>
      <!-- Block Content -->
      <!-- END Block Content -->
    </div>
    <!-- END Block -->
  </div>
</template>

<script>
import axios from "axios";
import { markRaw } from "vue";
import { Grid, h, html } from "gridjs";
import "gridjs/dist/theme/mermaid.css";
import { idID } from "gridjs/l10n";

import loadingBar from "@/assets/img/Moving_train.gif";

import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

import JsonExcel from "vue-json-excel3";

export default {
  components: {
    downloadExcel: JsonExcel,
    // CmpSelect2,
    // LoadingX,
    // CmpInputText,
    // CmpInputText,
  },
  data() {
    return {
      allProfiles: [],
      modalFiveTahun: false,
      selectedEmployee: null,
      isFiveYearChecking: false,
      modalTakeOut: false, // Untuk menampilkan modal
      takeOutForm: {
        profiles_id: "",
        tgl_take_out: "",
        ket: "",
      },
      access_page: this.$root.decryptData(localStorage.getItem("halaman")),
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      activemenu: null,
      grid: new Grid(),
      // grid2: new Grid(),
      errorField: {
        profiles_id: false,
        tgl_take_out: false,
        ket: false,
      },

      userid: 0,
      status_table: false,

      modal: false,

      todo: {
        profiles_id: "",
        tgl_take_out: "",
        ket: "",
      },

      flagButtonAdd: true,

      selectedprofiles: null,
      profiles: [],

      data_x_table: [],
      data_x_excel: [],
      nama_excelnya: "Data Profile Karyawan.xlsx",
      nama_sheetnya: "Data Karyawan Detail",
      json_data: [],
      json_meta: [[{ key: "charset", value: "UTF-8" }]],
      json_fields: {
        NO: "nomor",
        CABANG: "cabang",
        "NAMA KARYAWAN": "nama_karyawan",
        NIK: "nik",
        JABATAN: "jabatan",
        UNIT: "unit",
        "MASA KERJA KE-": "masa_kerja_ke",
        "TOTAL MASA KERJA": "total_masa_kerja",
        "KETERANGAN TAKE OUT": "keterangan_take_out"
        // KETERANGAN: "keterangan",
      },
      nama_sheetnya: "Data Karyawan", // Nama sheet Excel
    };
  },
  async mounted() {
    // await this.$root.refreshToken(localStorage.getItem("token"));
    this.userid = this.$root.get_id_user(localStorage.getItem("unique"));
    this.getTable();
    await this.checkFiveYearEmployees();
    await this.getProfile();
    // try {
    //   await this.$nextTick();
    //   // Satu handler global untuk export
    //   window.takeOutButton = (id) => {
    //     console.log("Export clicked:", { id });
    //     this.selectedId = id;
    //     this.modalTakeOut = true; // Menggunakan variable Vue untuk kontrol modal
    //   };
    // } catch (error) {
    //   console.error("Error in mounted:", error);
    // }
  },
  methods: {
    async checkFiveYearEmployees() {
      if (this.isFiveYearChecking) return;

      try {
        this.isFiveYearChecking = true;

        // Ambil semua data tanpa limit untuk pengecekan 5 tahun
        const response = await axios.get(
          this.$root.apiHost + "api/profiledetaildata",
          {
            params: {
              limit: 999999, // Selalu gunakan limit besar untuk pengecekan
              offset: 0,
            },
          }
        );

        // Ambil profilesalldata untuk data lengkap
        const profilesResponse = await axios.get(
          this.$root.apiHost + "api/profilesalldata"
        );

        if (response.data?.results) {
          // Filter untuk mendapatkan karyawan dengan masa kerja 5 tahun
          const fiveYearEmployee = response.data.results.find(
            (emp) =>
              this.checkFiveYearTerm(emp) && emp.sts_take_out !== "Tidak Aktif"
          );

          if (fiveYearEmployee) {
            // Cari data profil lengkap dari profilesalldata
            const profileData = profilesResponse.data.find(
              (p) => p.profile_name === fiveYearEmployee.profiles_id
            );

            if (profileData) {
              // Gabungkan data
              this.selectedEmployee = {
                ...fiveYearEmployee,
                profiles_id: profileData.profile_name,
                jabatan_id: profileData.jabatan_id,
                cabang_id: profileData.cabang_id,
                unit_id: profileData.unit_id,
              };

              this.modalFiveTahun = true;
            }
          }
        }
      } catch (error) {
        console.error("Error checking five year employees:", error);
        toast.error("Gagal mengecek data karyawan", { theme: "colored" });
      } finally {
        this.isFiveYearChecking = false;
      }
    },

    checkFiveYearTerm(data) {
      const tahun = parseInt(data.tahun) || 0;
      const bulan = parseInt(data.bulan) || 0;

      // Perhitungan lebih akurat dengan mempertimbangkan bulan
      return tahun > 5 || (tahun === 5 && bulan >= 0);
    },

    async saveTakeOutFiveTahun() {
      try {
        if (!this.todo.tgl_take_out) {
          toast.error("Tanggal take out harus diisi", { theme: "colored" });
          return;
        }

        await axios.post(this.$root.apiHost + "api/takeout-five-year", {
          profiles_id: this.selectedEmployee.profiles_id,
          tgl_take_out: this.todo.tgl_take_out,
          ket: "Habis Kontrak",
          userid: this.userid,
        });

        // Reset form dan tutup modal
        this.todo.tgl_take_out = "";
        this.modalFiveTahun = false;
        this.selectedEmployee = null;

        // Refresh table dan tampilkan notifikasi
        toast.success("Proses take out berhasil dilakukan", {
          theme: "colored",
        });
      } catch (error) {
        console.error("Gagal menyimpan Take Out:", error);
        toast.error(
          error.response?.data?.message || "Gagal melakukan take out",
          { theme: "colored" }
        );
      }
    },

    // Tambahkan method untuk refresh pengecekan
    // async refreshFiveYearCheck() {
    //   await this.checkFiveYearEmployees();
    // },

    formatTanggalIndonesia(tgl) {
      if (!tgl) return "";
      const date = new Date(tgl);
      const namaBulan = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
      ];
      return `${date.getDate()} ${
        namaBulan[date.getMonth()]
      } ${date.getFullYear()}`;
    },

    getStatusIcon(isInactive) {
      if (isInactive === undefined || isInactive === null) return "";
      return isInactive === "Tidak Aktif"
        ? '<i class="fa fa-check-circle text-success" style="font-size: 20px;"></i>'
        : "";
    },

    formatTakeoutInfo(ket, tglTakeOut) {
      if (!ket || !tglTakeOut) return "-";
      try {
        const formattedDate = this.formatDateIndonesia(tglTakeOut);
        return formattedDate
          ? `Ditakeout - ${ket} - pada ${formattedDate}`
          : "-";
      } catch (error) {
        console.error("Error formatting takeout info:", error);
        return "-";
      }
    },

    async saveTakeOut() {
      try {
        // Lakukan request untuk menyimpan take out
        // Lakukan request untuk menyimpan take out ke hr_kontrak
        await axios.post(this.$root.apiHost + "api/takeout", {
          profiles_id: this.todo.profiles_id,
          tgl_take_out: this.todo.tgl_take_out,
          ket: this.todo.ket,
        });

        // Reset form dan tutup modal
        this.todo.tgl_take_out = "";
        this.todo.ket = "";
        this.modalTakeOut = false;

        alert("STS Take Out berhasil disimpan ke hr_kontrak");
      } catch (error) {
        console.error("Gagal menyimpan Take Out:", error);
        alert("Gagal menyimpan Take Out.");
      }
    },

    padTo2Digits(num) {
      return num.toString().padStart(2, "0");
    },
    formatDate(date) {
      return (
        [
          date.getFullYear(),
          this.padTo2Digits(date.getMonth() + 1),
          this.padTo2Digits(date.getDate()),
        ].join("-") +
        " " +
        [
          this.padTo2Digits(date.getHours()),
          this.padTo2Digits(date.getMinutes()),
          this.padTo2Digits(date.getSeconds()),
        ].join(":")
      );
    },

    async getDataExportExcel() {
      try {
        this.$root.presentLoading();

        // Mengambil data detail profil
        const responseDetail = await axios.get(
          `${this.$root.apiHost}api/profiledetaildata`,
          {
            params: {
              limit: 999999,
              offset: 0,
            },
          }
        );

        // Mengambil data profil lengkap
        const responseProfile = await axios.get(
          `${this.$root.apiHost}api/profilesalldata`
        );

        if (responseDetail.data?.results && responseProfile.data) {
          // Membuat map data profil untuk pencarian cepat
          const profilMap = new Map(
            responseProfile.data.map((profil) => [profil.profile_name, profil])
          );

          // Format data untuk Excel
          this.json_data = responseDetail.data.results.map((item, index) => {
            const dataProfil = profilMap.get(item.profiles_id);

            // Menyiapkan total masa kerja
            const totalMasaKerja = `${item.tahun || 0} Tahun ${
              item.bulan || 0
            } Bulan ${item.hari || 0} Hari`;

            // Menyiapkan keterangan take out
            let keteranganTakeOut = "-";
            if (
              item.sts_take_out === "Tidak Aktif" &&
              item.keterangan_takeout
            ) {
              keteranganTakeOut = item.keterangan_takeout;
            }

            // Menyiapkan masa kerja
            let masaKerjaKe = item.masa_kerja_ke || "";
            if (item.sts_take_out === "Tidak Aktif") {
              masaKerjaKe = `${masaKerjaKe}(Take Out)`;
            }

            return {
              nomor: index + 1,
              cabang: dataProfil?.cabang_id || "",
              nama_karyawan: item.profiles_id || "",
              nik: dataProfil?.nik || "",
              jabatan: dataProfil?.jabatan_id || "",
              unit: dataProfil?.unit_id || "",
              masa_kerja_ke: masaKerjaKe,
              total_masa_kerja: totalMasaKerja,
              keterangan_take_out: keteranganTakeOut,
            };
          });

          // Set nama file Excel
          this.nama_excelnya = "Data Profile Karyawan.xlsx";

          console.log("Data Excel:", this.json_data);
          this.$root.stopLoading();
          return true;
        }
        throw new Error("Tidak ada data yang diterima dari API");
      } catch (error) {
        console.error("Error saat mengambil data export:", error);
        toast.error(
          "Gagal mengambil data untuk export: " +
            (error.message || "Error tidak diketahui"),
          {
            theme: "colored",
          }
        );
        this.$root.stopLoading();
        return false;
      }
    },

    formatTanggalIndonesia(tanggal) {
      if (!tanggal) return "";

      const namaBulan = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
      ];

      const date = new Date(tanggal);
      const tanggalnya = date.getDate();
      const bulannya = namaBulan[date.getMonth()];
      const tahunnya = date.getFullYear();

      return `${tanggalnya} ${bulannya} ${tahunnya}`;
    },

    async startDownload() {
      console.log("Starting download process..."); // Debug log
      const success = await this.getDataExportExcel();
      if (!success) {
        throw new Error("Failed to get export data");
      }
    },

    finishDownload() {
      console.log("Download finished"); // Debug log
      toast.success("Export Excel berhasil", { theme: "colored" });
    },

    download_excel_xyz() {},

    onChangeProfile(selectedProfile) {
      if (selectedProfile) {
        this.todo.profiles_id = selectedProfile; // Mengisi profiles_id dengan ID yang dipilih
      }
    },

    async getProfile() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "/api/profiledetailalldata"
        );
        this.profiles = response.data; // Menyimpan data profiles ke dalam array `profiles`
      } catch (error) {
        console.error("Error fetching profiles:", error);
      }
    },
    mySelectEvent() {
      this.todo.roles = this.tmp.cboRoles.code;
    },
    resetForm() {
      var mythis = this;
      Object.keys(mythis.errorField).forEach(function (key) {
        mythis.errorField[key] = false;
        mythis.todo[key] = "";
        //mythis.todo2[key] = "";
      });
      mythis.errorList = "";
    },
    refreshTable() {
      var mythis = this;
      mythis.status_table = false;
      //////////////////////////////
      $("#wrapper2").remove();
      var e = $('<div id="wrapper2"></div>');
      $("#box").append(e);
      this.getTable();
      //////////////////////////////
    },
    saveTodo() {
      var mythis = this;

      Swal.fire({
        title: "Create Data Sallary",
        text: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        cancelButtonText: "Cancel",
      }).then((result) => {
        if (result.isConfirmed) {
          /////////////////////////////////////////////////////////////////////
          mythis.$root.presentLoading();
          mythis.$root.flagButtonLoading = true;
          // const AuthStr = "bearer " + localStorage.getItem("token");
          // const config = {
          //   headers: {
          //     Authorization: AuthStr,
          //   },
          // };
          const config = "";
          var url = mythis.$root.apiHost + "api/profiledetaildata";
          axios
            .post(
              url,
              {
                profiles_id: mythis.todo.profiles_id,
                masa_kerja_ke: mythis.todo.masa_kerja_ke,
                tahun: mythis.todo.tahun,
                bulan: mythis.todo.bulan,
                hari: mythis.todo.hari,
                userid: mythis.userid,
              },
              config
            )
            .then((res) => {
              Swal.fire("Created!", res.data.message, "success");
              mythis.$root.stopLoading();
              mythis.$root.flagButtonLoading = false;
              mythis.resetForm();
              mythis.show_modal();
              mythis.refreshTable();
              //   mythis.$root.sendNotifFirebase(
              //     "Notifikasi Pesan",
              //     "Created Successfully"
              //   );
            })
            .catch(function (error) {
              mythis.$root.flagButtonLoading = false;
              mythis.$root.stopLoading();
              if (error.response) {
                //console.log(error.response.data);
                if (error.response.status == 422) {
                  mythis.errorList = error.response.data;
                  // mythis.$root.loader = false;
                  if (Object.keys(mythis.errorList).length > 0) {
                    //refresh semua menjadi false
                    Object.keys(mythis.errorField).forEach(function (key) {
                      mythis.errorField[key] = false;
                    });
                    //membuat jika error jadi true
                    Object.keys(mythis.errorList).forEach(function (key) {
                      toast.error(mythis.errorList[key], { theme: "colored" });

                      // const myArray = key.split(".");
                      // mythis.errorField[myArray[1]] = true;
                      mythis.errorField[key] = true;
                    });
                  }
                } else {
                  toast.error(error.response.data.message, {
                    theme: "colored",
                  });
                }
              } else if (error.request) {
                console.log(error.request);
              } else {
                console.log("Error", error.message);
              }
            });
          /////////////////////////////////////////////////////////////////////
        }
      });
    },
    show_modal() {
      this.modal = !this.modal;
      if (this.modal == false) {
        this.flagButtonAdd = true;
        this.resetForm();
      }
    },
    async jqueryDelEdit() {
      const mythis = this;
      $(document).on("click", "#editData", async function () {
        let id = $(this).data("id");
        await mythis.getData(id);
        mythis.show_modal();
      });
      $(document).on("click", "#deleteData", function () {
        let id = $(this).data("id");
        mythis.deleteTodo(id);
      });
      $(document).on("click", ".takeOutButton", function (e) {
        e.preventDefault();
        console.log("Export button clicked");

        try {
          const rowData = JSON.parse($(this).attr("data-row"));
          console.log("Parsed row data:", rowData);

          mythis.selectedId = rowData.id;

          console.log("Selected data:", {
            id: mythis.selectedId,
          });

          // Langsung panggil toggleModal
          mythis.$nextTick(() => {
            $("#modalTakeOut").modal("show");
          });
        } catch (error) {
          console.error("Error processing click:", error);
        }
      });
    },
    getTable() {
      var mythis = this;
      this.grid = new Grid();
      this.grid.updateConfig({
        // // language: idID,
        // pagination: {
        //   limit: mythis.$root.pagingTabel1,
        //   server: {
        //     url: (prev, page, limit) =>
        //       `${prev}${prev.includes("?") ? "&" : "?"}limit=${limit}&offset=${
        //         page * limit
        //       }`,
        //   },
        // },
        search: {
          enabled: true, // Mengaktifkan fitur search
          server: {
            url: (prev, keyword) => {
              const baseUrl = mythis.$root.apiHost + "api/profiledetaildata";
              if (keyword) {
                // Konversi keyword ke lowercase sebelum dikirim ke server
                const searchTerm = keyword.toLowerCase();
                return `${baseUrl}?search=${encodeURIComponent(
                  searchTerm
                )}&limit=999999`;
              }
              return `${baseUrl}?limit=999999`;
            },
          },
        },
        columns: [
          { name: "ID", hidden: true },
          "No",
          "Nama Karyawan",
          "Masa Kerja Ke",
          "Tahun",
          "Bulan",
          "Hari",
          {
            name: "Status Aktif",
            formatter: (_, row) => {
              const status = row.cells[7]?.data; // Sesuaikan indeks dengan posisi data status_aktif
              if (status === "Aktif") {
                return html(`
              <div class="d-flex align-items-center justify-content-center">
                <span class="text-success mr-2">Aktif</span>
                <i class="fa fa-check-circle text-success" style="font-size: 16px;"></i>
              </div>
            `);
              } else {
                return html(`
              <div class="d-flex align-items-center justify-content-center">
                <span class="text-danger mr-2">Tidak Aktif</span>
                <i class="fa fa-times-circle text-danger" style="font-size: 16px;"></i>
              </div>
            `);
              }
            },
          },
          {
            name: "Status Take Out",
            formatter: (_, row) => {
              const stsValue = row.cells[7]?.data; // mengambil sts_take_out
              if (stsValue && stsValue.toLowerCase() === "tidak aktif") {
                return html(`
              <span class="text-center">
                <i class="fa fa-check-circle text-success" style="font-size: 20px;"></i>
              </span>
            `);
              }
              return "";
            },
          },
          {
            name: "Keterangan Take Out",
            width: "400px",
            formatter: (_, row) => {
              const statusAktif = row.cells[7]?.data;
              const ketTakeOut = row.cells[9]?.data; // Sesuaikan indeks sesuai posisi data

              if (statusAktif === "Tidak Aktif") {
                return html(`
                <div class="takeout-info" style="
                    text-align: center;
                    padding: 8px;
                    white-space: normal;
                    line-height: 1.5;
                    min-width: 380px;
                ">
                    ${ketTakeOut || "-"}
                </div>
            `);
              }
              return "-";
            },
          },
        ],

        // {
        //   name: "Action",
        //   formatter: (_, row) =>
        //     mythis.$root.accessRoles[mythis.access_page].update &&
        //     mythis.$root.accessRoles[mythis.access_page].delete
        //       ? html(
        //           `
        //           <button data-id="${row.cells[0].data}" class="btn btn-sm btn-warning text-white" id="editData" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil-square-o"></i></button>
        //           &nbsp;&nbsp;&nbsp;
        //           <button data-id="${row.cells[0].data}" class="btn btn-sm btn-danger text-white" id="deleteData" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash-o"></i></button>
        //         `
        //         )
        //       : mythis.$root.accessRoles[mythis.access_page].update
        //       ? html(
        //           `
        //           <button data-id="${row.cells[0].data}" class="btn btn-sm btn-warning text-white" id="editData" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil-square-o"></i></button>`
        //         )
        //       : mythis.$root.accessRoles[mythis.access_page].delete
        //       ? html(`&nbsp;&nbsp;&nbsp;
        //           <button data-id="${row.cells[0].data}" class="btn btn-sm btn-danger text-white" id="deleteData" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash-o"></i></button>`)
        //       : ``,
        // },

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
          },
        },
        server: {
          url: this.$root.apiHost + "api/profiledetaildata?limit=999999",
          then: (data) => {
            let nomorBaris = 1;
            return data.results.map((card) => {
              let masaKerjaKe = card.masa_kerja_ke || "";
              if (card.sts_take_out === "Tidak Aktif") {
                masaKerjaKe = `${masaKerjaKe}(Take Out)`;
              }

              return [
                card.id,
                nomorBaris++,
                html(`<span class="text-center">${card.profiles_id}</span>`),
                html(`<span class="text-center">${masaKerjaKe}</span>`),
                html(`<span class="text-center">${card.tahun || 0}</span>`),
                html(`<span class="text-center">${card.bulan || 0}</span>`),
                html(`<span class="text-center">${card.hari || 0}</span>`),
                card.status_aktif || "",
                card.sts_take_out || "",
                card.keterangan_takeout || "",
                // card.ket || "",
                // card.tgl_take_out || "",
                // card.ket_take_out || "",
                // card.tgl_take_out || "",
                // card.keterangan_takeout || "", // Tambahkan ini
              ];
            });
          },
          total: (data) => data.count,
          handle: (res) => {
            if (res.status === 404) return { data: [] };
            if (res.ok) return res.json();
            throw Error("oh no :(");
          },
        },
      });
      // DOM instead of vue selector because we are using vanilla JS
      this.grid.render(document.getElementById("wrapper2"));
      this.number = 0;

      $(document).off("click", "#editData");
      $(document).off("click", "#deleteData");
      mythis.jqueryDelEdit();
      this.status_table = true;
    },

    deleteTodo(id) {
      var mythis = this;
      Swal.fire({
        title: "Delete Data",
        text: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        cancelButtonText: "Cancel",
      }).then((result) => {
        if (result.isConfirmed) {
          mythis.$root.presentLoading();
          // const AuthStr = "bearer " + localStorage.getItem("token");
          const config = {
            // headers: {
            //   Authorization: AuthStr,
            // },
            data: {
              fileUpload: "form satuan",
              userid: mythis.userid,
            },
          };
          // const config = "";
          axios
            .delete(
              mythis.$root.apiHost + `api/profiledetaildata/${id}`,
              config
            )
            .then((res) => {
              //console.log(res.data.data);
              // /Swal.fire("Terhapus!", "Data telah sukses dihapus", "success");
              Swal.fire("Deleted!", "Data has been deleted", "success");

              mythis.$root.stopLoading();
              mythis.refreshTable();
              mythis.resetForm();
            });
        }
      });
    },
    editTodo() {
      var mythis = this;
      mythis.$root.flagButtonLoading = true;
      // const AuthStr = "bearer " + localStorage.getItem("token");
      const config = {
        // headers: {
        //   Authorization: AuthStr,
        // },
      };
      axios
        .put(
          mythis.$root.apiHost + "api/profiledetaildata/" + mythis.todo.id,
          {
            profiles_id: mythis.todo.profiles_id,
            masa_kerja_ke: mythis.todo.masa_kerja_ke,
            tahun: mythis.todo.tahun,
            bulan: mythis.todo.bulan,
            hari: mythis.todo.hari,
            userid: mythis.userid,
          },
          config
        )
        .then((res) => {
          //console.log(res);
          //alert(res.data.message);
          Swal.fire("Updated!", res.data.message, "success");
          mythis.$root.flagButtonLoading = false;
          mythis.resetForm();
          mythis.show_modal();
          mythis.refreshTable();
        })
        .catch(function (error) {
          mythis.$root.flagButtonLoading = false;
          if (error.response) {
            //console.log(error.response.data);
            if (error.response.status == 422) {
              mythis.errorList = error.response.data;
              mythis.$root.loader = false;
              if (Object.keys(mythis.errorList).length > 0) {
                //refresh semua menjadi false
                Object.keys(mythis.errorField).forEach(function (key) {
                  mythis.errorField[key] = false;
                });
                //membuat jika error jadi true
                Object.keys(mythis.errorList).forEach(function (key) {
                  toast.error(mythis.errorList[key], { theme: "colored" });

                  // const myArray = key.split(".");
                  // mythis.errorField[myArray[1]] = true;
                  mythis.errorField[key] = true;
                });
              }
            } else {
              toast.error(error.response.data.message, {
                theme: "colored",
              });
            }
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    async getData(id) {
      var mythis = this;
      mythis.flagButtonAdd = false;
      mythis.$root.presentLoading();
      mythis.todo = {};
      const AuthStr = "bearer " + localStorage.getItem("token");
      const config = {
        // headers: {
        //   Authorization: AuthStr,
        // },
      };
      await axios
        .get(mythis.$root.apiHost + `api/profiledetaildata/${id}`, config)
        .then(async (res) => {
          //console.log(res.data.data);
          //mythis.acuanEdit = id;
          //mythis.todo = res.data.data;
          mythis.todo.id = id;
          mythis.todo.profiles_id = res.data.data.profiles_id;
          mythis.todo.masa_kerja_ke = res.data.data.masa_kerja_ke;
          mythis.todo.tahun = res.data.data.tahun;
          mythis.todo.bulan = res.data.data.bulan;
          mythis.todo.hari = res.data.data.hari;

          document.getElementById("profile_id").focus(); // sets the focus on the input

          mythis.$root.stopLoading();
        });
    },
  },
};
</script>

<style scoped>
.input-error {
  border: red 1px solid;
}
</style>
