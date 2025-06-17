<template>
  <div id="page-content" v-if="isLogin == 1" style="min-height: 850px">
    <div class="block">
      <div class="block-title">
        <h2>
          <strong>MENU {{ $root.judulHalaman }}</strong>
        </h2>
        <i v-if="!status_table" class="fa fa-spinner fa-spin text-default"></i>
      </div>

      <div class="block-content">
        <button
          v-if="status_table && $root.accessRoles[access_page].create"
          class="btn btn-sm btn-primary pull-right"
          @click="show_modal()"
        >
          ADD DATA
        </button>

        <div id="wrapper2"></div>
        <div id="box"></div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div
    :class="modal ? 'modal fade in' : 'modal fade'"
    id="exampleModalCenter"
    tabindex="-1"
    role="dialog"
    aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true"
    data-keyboard="false"
    data-backdrop="true"
    :style="modal ? 'display: block;' : 'display: none;'"
  >
    <div class="modal-dialog2 modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          {{ flagButtonAdd ? "ADD" : "UPDATE" }} DATA {{ $root.judulHalaman }}
          <button
            id="closeModal"
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
            :disabled="$root.flagButtonLoading"
            @click="show_modal()"
          >
            <span aria-hidden="true">X</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nama Karyawan</label>
                  <v-select
                    id="profile_id"
                    variant="outlined"
                    label="profile_name"
                    :options="profiles"
                    placeholder="Pilih Karyawan"
                    v-model="selectedProfile"
                    :reduce="
                      (option) => ({
                        profile_name: option.profile_name,
                        id: option.id,
                      })
                    "
                    @update:modelValue="onChangeProfile"
                  >
                    <template #option="{ profile_name }">
                      {{ profile_name }}
                    </template>
                  </v-select>
                </div>
                <div
                  class="form-group"
                  v-if="activeKriteria.includes('Grooming')"
                >
                  <label>Grooming</label>
                  <CmpInputText
                    type="text"
                    placeholder="Grooming"
                    v-model="todo.grooming"
                    :class="
                      errorField.grooming
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                  />
                </div>
                <div class="form-group">
                  <label>Bulan</label>
                  <select
                    v-model="todo.mop"
                    class="form-control input-lg"
                    :class="errorField.mop ? 'input-error' : ''"
                  >
                    <option value="" disabled>Pilih Bulan Evaluasi</option>
                    <option
                      v-for="month in monthOptions"
                      :key="month"
                      :value="month"
                    >
                      {{ month }}
                    </option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Tahun</label>
                  <select
                    v-model="todo.yop"
                    class="form-control input-lg"
                    :class="errorField.yop ? 'input-error' : ''"
                  >
                    <option value="" disabled>Pilih Tahun Evaluasi</option>
                    <option
                      v-for="year in yearOptions"
                      :key="year"
                      :value="year"
                    >
                      {{ year }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div
                  class="form-group"
                  v-if="activeKriteria.includes('Kedisiplinan dan Kehadiran')"
                >
                  <label>Kedisiplinan</label>
                  <CmpInputText
                    type="text"
                    placeholder="Kedisiplinan"
                    v-model="todo.disiplin"
                    :class="
                      errorField.disiplin
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                  />
                </div>
                <div
                  class="form-group"
                  v-if="activeKriteria.includes('Administrasi')"
                >
                  <label>Administrasi</label>
                  <CmpInputText
                    type="text"
                    placeholder="Administrasi"
                    v-model="todo.admin"
                    :class="
                      errorField.admin
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                  />
                </div>
                <div
                  class="form-group"
                  v-if="activeKriteria.includes('Pengetahuan Produk')"
                >
                  <label>Pengetahuan Produk</label>
                  <CmpInputText
                    type="text"
                    placeholder="Pengetahuan Produk"
                    v-model="todo.pk"
                    :readonly="true"
                    :class="
                      errorField.pk
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                  />
                </div>
                <div
                  class="form-group"
                  v-if="activeKriteria.includes('Pencapaian Target Sales')"
                >
                  <label>Pencapaian Target Sales</label>
                  <CmpInputText
                    type="text"
                    placeholder="Pencapaian Target Sales"
                    v-model="todo.soba"
                    :readonly="true"
                    :class="
                      errorField.soba
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <div class="form-group form-actions">
              <div class="col-xs-12">
                <button
                  v-if="flagButtonAdd"
                  @click="saveTodo()"
                  type="button"
                  class="btn btn-sm btn-primary pull-left"
                  :disabled="$root.flagButtonLoading || !isFormValid"
                >
                  <i
                    v-if="$root.flagButtonLoading"
                    class="fa fa-spinner fa-spin text-default"
                  ></i>
                  SAVE DATA
                </button>

                <button
                  v-if="!flagButtonAdd"
                  @click="editTodo()"
                  type="button"
                  class="btn btn-sm btn-primary pull-left"
                  :disabled="$root.flagButtonLoading || !isFormValid"
                >
                  <i
                    v-if="$root.flagButtonLoading"
                    class="fa fa-spinner fa-spin text-default"
                  ></i>
                  UPDATE DATA
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { Grid, h, html } from "gridjs";
import "gridjs/dist/theme/mermaid.css";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";

export default {
  components: {
    vSelect,
  },
  data() {
    return {
      access_page: this.$root.decryptData(localStorage.getItem("halaman")),
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      userid: this.$root.get_id_user(),
      status_table: false,
      grid: new Grid(),
      modal: false,
      flagButtonAdd: true,
      todo: {
        id: null,
        profiles_id: "",
        grooming: "",
        pk: "",
        soba: "",
        disiplin: "",
        admin: "",
        mop: "",
        yop: "",
      },
      errorField: {
        profiles_id: false,
        grooming: false,
        pk: false,
        soba: false,
        disiplin: false,
        admin: false,
        mop: false,
        yop: false,
      },

      selectedProfile: null,
      profiles: [],

      monthOptions: [],
      yearOptions: [],

      sobaData: {
        tb: null,
        bb: null,
        nama_toko: null,
      },

      activeKriteria: [],
    };
  },

  computed: {
    currentUserId() {
      return this.userid || this.$root.get_id_user();
    },
    isFormValid() {
      const requiredFields = {
        profiles_id: true,
        mop: true,
        yop: true,
      };

      // Tambahkan kriteria aktif ke dalam requiredFields
      this.activeKriteria.forEach((kriteria) => {
        switch (kriteria) {
          case "Grooming":
            requiredFields.grooming = true;
            break;
          case "Pengetahuan Produk":
            requiredFields.pk = true;
            break;
          case "Pencapaian Target Sales":
            requiredFields.soba = true;
            break;
          case "Kedisiplinan dan Kehadiran":
            requiredFields.disiplin = true;
            break;
          case "Administrasi":
            requiredFields.admin = true;
            break;
        }
      });

      return Object.keys(requiredFields).every((field) => this.todo[field]);
    },
  },

  async mounted() {
    console.log(
      "Setting userid:",
      this.$root.get_id_user(localStorage.getItem("unique"))
    );
    this.userid = this.$root.get_id_user(localStorage.getItem("unique"));
    await this.getProfile();
    await this.getTable();
  },

  watch: {
    "todo.mop": {
      async handler(newMonth) {
        if (this.todo.profiles_id && newMonth) {
          try {
            // Get soba data for selected profile and month
            const response = await axios.get(
              this.$root.apiHost +
                `api/tempt_report_soba_pk?profiles_id=${this.todo.profiles_id}&mop=${newMonth}`
            );

            if (
              response.data &&
              response.data.results &&
              response.data.results[0]
            ) {
              const sobaData = response.data.results[0];
              // Store TB/BB data for PDF export
              this.sobaData = {
                tb: sobaData.tb,
                bb: sobaData.bb,
                nama_toko: sobaData.nama_toko,
              };

              // Set pk and soba values automatically from tempt_report_soba_pk
              this.todo.pk = sobaData.pk || "";
              this.todo.soba = sobaData.achv_soba || "";
            }
          } catch (error) {
            console.error("Error fetching soba data:", error);
          }
        }
      },
    },
  },

  methods: {
    async getActiveKriteria(jabatan_id) {
      try {
        const response = await axios.get(
          this.$root.apiHost +
            `api/detail-kriteria-rapor?jabatan_id=${jabatan_id}&is_active=1`
        );

        // Extract kriteria names langsung dari response
        this.activeKriteria = response.data.results
          .filter((item) => item.kriteria)
          .map((item) => item.kriteria.kriteria);

        console.log("Active kriteria:", this.activeKriteria);
      } catch (error) {
        console.error("Error fetching active kriteria:", error);
        toast.error("Gagal mengambil data kriteria aktif");
      }
    },

    async fetchMonthYearOptions(profileId) {
      try {
        const response = await axios.get(
          this.$root.apiHost +
            `api/tempt_report_soba_pk?profiles_id=${profileId}`
        );

        if (response.data && Array.isArray(response.data.results)) {
          // Get unique months and years
          const uniqueMonths = [
            ...new Set(
              response.data.results
                .map((item) => item.mop)
                .filter(Boolean) // Filter out null/undefined
                .sort()
            ),
          ]; // Sort months alphabetically
          const uniqueYears = [
            ...new Set(
              response.data.results
                .map((item) => item.yop)
                .filter(Boolean)
                .sort()
            ),
          ];
          console.log("Available months:", uniqueMonths);
          console.log("Available years:", uniqueYears);

          this.monthOptions = uniqueMonths;
          this.yearOptions = uniqueYears;

          // Reset selected values
          this.todo.mop = "";
          this.todo.yop = "";
        }
      } catch (error) {
        console.error("Error fetching month/year options:", error);
        toast.error("Gagal mengambil data bulan dan tahun", {
          theme: "colored",
        });
      }
    },

    async exportRaporPDF(data) {
      try {
        // Get additional data from profiles and temp_report_soba_pk
        const profileResponse = await axios.get(
          this.$root.apiHost + `api/profilesalldata`
        );
        const sobaResponse = await axios.get(
          this.$root.apiHost + `api/tempt_report_soba_pk`
        );

        const sobaDataArray = Array.isArray(sobaResponse.data)
          ? sobaResponse.data
          : sobaResponse.data.results || [];

        // Find related profile data
        const profileData = profileResponse.data.find(
          (p) => p.profile_name === data.profiles_id
        );

        // Find related soba data
        const sobaData = sobaDataArray.find(
          (s) => s.profiles_id === data.profiles_id
        );

        // Calculate age from birthdate
        const age = profileData?.birthdate
          ? Math.floor(
              (new Date() - new Date(profileData.birthdate)) / 31557600000
            )
          : "-";

        // Header section modifications
        const doc = new jsPDF("p", "pt", "a4");
        const pageWidth = doc.internal.pageSize.getWidth();
        const margin = 40;

        // Add dark blue header background
        doc.setFillColor(19, 41, 112); // Darker blue color
        doc.rect(0, 0, pageWidth, 80, "F");

        // Center logo and text calculations
        const logoWidth = 80; // Increased logo width
        const logoHeight = 40; // Increased logo height
        const textWidth = 150; // Increased text block width
        const totalWidth = logoWidth + 20 + textWidth;
        const startX = (pageWidth - totalWidth) / 2;

        // Add centered larger logo
        doc.addImage(
          "/src/assets/img/logo-sgs.png",
          "PNG",
          startX,
          20, // Adjusted Y position
          logoWidth,
          logoHeight
        );

        // Add centered "SINERGI GLOBAL SERVIS" text with larger font and bold
        doc.setFontSize(18); // Increased font size
        doc.setFont(undefined, "bold"); // Make text bold
        doc.setTextColor(255, 255, 255);
        doc.text("SINERGI", startX + logoWidth + 20, 35);
        doc.text("GLOBAL", startX + logoWidth + 20, 50);
        doc.text("SERVIS", startX + logoWidth + 20, 65);

        // Reset font style for rest of document
        doc.setFont(undefined, "normal");

        // Add white section for title
        doc.setFillColor(255, 255, 255);
        doc.rect(0, 80, pageWidth, 50, "F");

        // Center the title text with larger size and blue color
        doc.setFontSize(24); // Increased font size
        doc.setFont(undefined, "bold"); // Make text bold
        doc.setTextColor(19, 41, 112); // Same blue as header
        doc.text("RAPORT BEAUTY ADVISOR", pageWidth / 2, 110, {
          align: "center",
        });
        // Reset font style for rest of document
        doc.setFont(undefined, "normal");

        // Add horizontal line under centered title
        doc.setDrawColor(200, 200, 200);
        doc.line(margin, 120, pageWidth - margin, 120);

        // Reset text color and font for content
        doc.setTextColor(0, 0, 0);
        doc.setFontSize(11);

        // Personal Information Section
        let currentY = 140;
        const leftCol = margin;
        const rightCol = margin + 80;

        // Add employee details
        const details = [
          ["Nama", data.profiles_id || "-"],
          ["Usia", `${age} Tahun`],
          ["TB/BB", `${sobaData?.tb || "-"}/${sobaData?.bb || "-"}`],
          ["Cabang", profileData?.cabang_id || "-"],
          [
            "Join Date",
            new Date(profileData?.join_date || "").toLocaleDateString("id-ID"),
          ],
          ["Area Coverage", sobaData?.nama_toko || "-"],
        ];

        details.forEach(([label, value]) => {
          doc.text(label, leftCol, currentY);
          doc.text(":", rightCol, currentY);
          doc.text(value, rightCol + 20, currentY);
          currentY += 20;
        });

        // Criteria Table
        currentY += 20;

        // Define table headers with blue background
        const tableHeaders = [
          [
            {
              content: "KRITERIA",
              styles: {
                fillColor: [25, 57, 138],
                textColor: [255, 255, 255],
                fontStyle: "bold",
                halign: "left",
              },
            },
            {
              content: "NILAI",
              styles: {
                fillColor: [25, 57, 138],
                textColor: [255, 255, 255],
                fontStyle: "bold",
                halign: "center",
              },
            },
          ],
        ];

        // Table data with alternating background
        const tableData = [
          ["Grooming", data.grooming || "-"],
          ["Pengetahuan Produk", data.pk || "-"],
          ["Pencapaian Target Sales", data.soba || "-"],
          ["Kedisiplinan dan Kehadiran", data.disiplin || "-"],
          ["Administrasi", data.admin || "-"],
        ];

        // Calculate wider table position
        const tableWidth = 500; // Increased total width
        const tableMargin = (pageWidth - tableWidth) / 2;

        // Add table with wider styling
        doc.autoTable({
          startY: currentY,
          head: tableHeaders,
          body: tableData,
          theme: "grid",
          styles: {
            fontSize: 11,
            cellPadding: 8,
          },
          columnStyles: {
            0: { cellWidth: 380 }, // Increased width for first column
            1: { cellWidth: 120, halign: "center" }, // Increased width for second column
          },
          alternateRowStyles: {
            fillColor: [240, 240, 240],
          },
          margin: { left: tableMargin, right: tableMargin },
          tableWidth: tableWidth,
        });

        // Scoring guide section
        currentY = doc.lastAutoTable.finalY + 30;

        // Center and color the scoring section header with larger font
        doc.setFontSize(14); // Increased font size
        doc.setFont(undefined, "bold"); // Make text bold
        doc.setTextColor(19, 41, 112); // Set blue color
        doc.text("KETERANGAN PENILAIAN", pageWidth / 2, currentY, {
          align: "center",
        });
        // Reset font style for rest of document
        doc.setFont(undefined, "normal");
        currentY += 8;

        // Add thicker blue line under the header with table width
        doc.setDrawColor(19, 41, 112);
        doc.setLineWidth(1.5); // Make line thicker
        doc.line(tableMargin, currentY, tableMargin + tableWidth, currentY); // Match table width
        doc.setLineWidth(1); // Reset line width
        currentY += 20;

        // Reset text color and size for scores
        doc.setTextColor(0, 0, 0);
        doc.setFontSize(11);

        // Score list with aligned equals signs
        const scores = [
          ["90-100", "Baik Sekali"],
          ["75-89", "Baik"],
          ["70-74", "Cukup"],
          ["50-69", "Kurang"],
        ];

        scores.forEach(([range, desc]) => {
          doc.text(range, tableMargin, currentY); // Align with table
          doc.text("=", tableMargin + 60, currentY);
          doc.text(desc, tableMargin + 75, currentY);
          currentY += 20;
        });

        // Add space before status
        currentY += 10;

        // Status section in blue
        doc.setTextColor(19, 41, 112);
        doc.text("STATUS", tableMargin, currentY);
        currentY += 20;
        doc.text("DIPERPANJANG / TIDAK DIPERPANJANG", tableMargin, currentY);

        // Signature section
        currentY += 60;
        doc.text("SUPERVISOR", pageWidth - margin - 100, currentY);

        // Add signature line only for supervisor
        currentY += 40;
        doc.setDrawColor(0, 0, 0);
        doc.line(
          pageWidth - margin - 200,
          currentY,
          pageWidth - margin,
          currentY
        );

        // Save the PDF
        const fileName = `Rapor_${data.profiles_id}_${data.mop}_${data.yop}.pdf`;
        doc.save(fileName);

        return true;
      } catch (error) {
        console.error("Error generating PDF:", error);
        throw error;
      }
    },

    // Add a wrapper method to handle the export button click
    // Add a wrapper method to handle the export button click
    async handleExportPDF(id) {
      try {
        this.$root.presentLoading();

        // Get rapor data
        const raporResponse = await axios.get(
          this.$root.apiHost + `api/hr_rapor/${id}`
        );

        if (!raporResponse.data || !raporResponse.data.data) {
          throw new Error("Data rapor tidak ditemukan");
        }

        const raporData = raporResponse.data.data;

        // Get soba data for the specific profile and month
        const sobaResponse = await axios.get(
          this.$root.apiHost +
            `api/tempt_report_soba_pk?profiles_id=${raporData.profiles_id}&mop=${raporData.mop}`
        );

        // Combine the data
        const pdfData = {
          ...raporData,
          tb: sobaResponse.data.results?.[0]?.tb || "-",
          bb: sobaResponse.data.results?.[0]?.bb || "-",
          bmi: sobaResponse.data.results?.[0]?.bmi || "-",
          nama_toko: sobaResponse.data.results?.[0]?.nama_toko || "2 Outlet",
        };

        await this.exportRaporPDF(pdfData);

        this.$root.stopLoading();
        toast.success("PDF berhasil dibuat", { theme: "colored" });
      } catch (error) {
        this.$root.stopLoading();
        toast.error(error.message || "Gagal membuat PDF", { theme: "colored" });
        console.error("Error:", error);
      }
    },

    async getProfile() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/profilesalldata"
        );
        this.profiles = response.data;
      } catch (error) {
        console.error("Error fetching profiles:", error);
        toast.error("Gagal mengambil data profiles", { theme: "colored" });
      }
    },

    async onChangeProfile() {
      try {
        if (!this.selectedProfile) {
          this.todo.profiles_id = "";
          this.monthOptions = [];
          this.yearOptions = [];
          this.todo.mop = "";
          this.todo.yop = "";
          this.activeKriteria = [];
          return;
        }

        this.todo.profiles_id = this.selectedProfile.profile_name;

        // Get jabatan_id dari profile
        const profileResponse = await axios.get(
          this.$root.apiHost + `api/profiles/${this.selectedProfile.id}`
        );

        const jabatan_id = profileResponse.data.data?.jabatan_id;
        if (jabatan_id) {
          await this.getActiveKriteria(jabatan_id);
        }

        await this.fetchMonthYearOptions(this.todo.profiles_id);
      } catch (error) {
        console.error("Error in onChangeProfile:", error);
        toast.error("Gagal memilih karyawan", { theme: "colored" });
      }
    },

    resetForm() {
      Object.keys(this.errorField).forEach((key) => {
        this.errorField[key] = false;
        this.todo[key] = "";
      });
      this.todo.id = null;
      this.selectedProfile = null;
      this.sobaData = {
        tb: null,
        bb: null,
        nama_toko: null,
      };
    },

    refreshTable() {
      this.status_table = false;
      $("#wrapper2").remove();
      var e = $('<div id="wrapper2"></div>');
      $("#box").append(e);
      this.getTable();
    },

    show_modal() {
      this.modal = !this.modal;
      if (!this.modal) {
        this.flagButtonAdd = true;
        this.resetForm();
      }
    },

    async jqueryDelEdit() {
      $(document).on("click", "#editData", async (e) => {
        let id = $(e.currentTarget).data("id");
        await this.getData(id);
        this.show_modal();
      });

      $(document).on("click", "#deleteData", (e) => {
        let id = $(e.currentTarget).data("id");
        this.deleteTodo(id);
      });

      $(document).on("click", "#exportPdf", (e) => {
        let id = $(e.currentTarget).data("id");
        this.handleExportPDF(id);
      });
    },

    getTable() {
      this.grid = new Grid({
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
          { name: "ID", hidden: true },
          "No",
          "Nama Karyawan",
          "Grooming",
          "Pengetahuan Produk",
          "Pencapaian Target Sales",
          "Kedisiplinan",
          "Administrasi",
          "Bulan",
          "Tahun",
          {
            name: "Action",
            formatter: (_, row) => {
              const exportButton = `
    <button 
      data-id="${row.cells[0].data}" 
      class="btn btn-sm btn-outline-danger"
      style="background-color: white;"
      id="exportPdf" 
      title="Export PDF">
      <i class="fa fa-file-pdf-o" style="color: red;"></i>
    </button>`;

              const editButton = `<button data-id="${row.cells[0].data}" class="btn btn-sm btn-warning text-white" id="editData"><i class="fa fa-pencil"></i></button>`;

              const deleteButton = `<button data-id="${row.cells[0].data}" class="btn btn-sm btn-danger text-white" id="deleteData"><i class="fa fa-trash"></i></button>`;

              return html(`${exportButton} ${editButton} ${deleteButton}`);
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
          },
        },
        server: {
          url: this.$root.apiHost + "api/hr_rapor",
          then: (data) =>
            data.results.map((item) => [
              item.id,
              data.nomorBaris++ + 1,
              html(`<span class="pull-left">${item.profiles_id}</span>`),
              html(`<span class="pull-left">${item.grooming}</span>`),
              html(`<span class="pull-left">${item.pk}</span>`),
              html(`<span class="pull-left">${item.soba}</span>`),
              html(`<span class="pull-left">${item.disiplin}</span>`),
              html(`<span class="pull-left">${item.admin}</span>`),
              html(`<span class="pull-left">${item.mop}</span>`),
              html(`<span class="pull-left">${item.yop}</span>`),
            ]),
          total: (data) => data.count,
        },
      });

      this.grid.render(document.getElementById("wrapper2"));

      $(document).off("click", "#editData");
      $(document).off("click", "#deleteData");
      this.jqueryDelEdit();
      this.status_table = true;
    },

    saveTodo() {
      Swal.fire({
        title: "Create Data Rapor",
        text: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        cancelButtonText: "Cancel",
      }).then((result) => {
        if (result.isConfirmed) {
          this.$root.presentLoading();
          this.$root.flagButtonLoading = true;

          axios
            .post(this.$root.apiHost + "api/hr_rapor", {
              ...this.todo,
              userid: this.currentUserId,
            })
            .then((res) => {
              Swal.fire("Created!", res.data.message, "success");
              this.$root.stopLoading();
              this.$root.flagButtonLoading = false;
              this.resetForm();
              this.show_modal();
              this.refreshTable();
            })
            .catch((error) => {
              this.$root.flagButtonLoading = false;
              this.$root.stopLoading();
              if (error.response) {
                if (error.response.status === 422) {
                  this.errorList = error.response.data;
                  Object.keys(this.errorField).forEach((key) => {
                    this.errorField[key] = false;
                  });
                  Object.keys(this.errorList).forEach((key) => {
                    toast.error(this.errorList[key], {
                      theme: "colored",
                      onClose: () => this.show_modal(false),
                      autoClose: 2500,
                    });
                    this.errorField[key] = true;
                  });
                } else {
                  toast.error(error.response.data.message, {
                    theme: "colored",
                    onClose: () => this.show_modal(false),
                    autoClose: 2500,
                  });
                }
              }
            });
        }
      });
    },

    editTodo() {
      this.$root.flagButtonLoading = true;

      axios
        .put(this.$root.apiHost + "api/hr_rapor/" + this.todo.id, {
          ...this.todo,
          userid: this.currentUserId,
        })
        .then((res) => {
          Swal.fire("Updated!", res.data.message, "success");
          this.$root.flagButtonLoading = false;
          this.resetForm();
          this.show_modal();
          this.refreshTable();
        })
        .catch((error) => {
          this.$root.flagButtonLoading = false;
          if (error.response) {
            if (error.response.status === 422) {
              this.errorList = error.response.data;
              Object.keys(this.errorField).forEach((key) => {
                this.errorField[key] = false;
              });
              Object.keys(this.errorList).forEach((key) => {
                toast.error(this.errorList[key], {
                  theme: "colored",
                });
                this.errorField[key] = true;
              });
            } else {
              toast.error(error.response.data.message, {
                theme: "colored",
              });
            }
          }
        });
    },

    deleteTodo(id) {
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
          this.$root.presentLoading();

          const config = {
            data: {
              userid: this.currentUserId,
            },
          };

          axios
            .delete(this.$root.apiHost + `api/hr_rapor/${id}`, config)
            .then((res) => {
              Swal.fire("Deleted!", "Data has been deleted", "success");
              this.$root.stopLoading();
              this.refreshTable();
              this.resetForm();
            })
            .catch((error) => {
              this.$root.stopLoading();
              toast.error(
                error.response?.data?.message || "Error deleting data",
                {
                  theme: "colored",
                }
              );
            });
        }
      });
    },

    async getData(id) {
      this.flagButtonAdd = false;
      this.$root.presentLoading();
      this.todo = {};

      try {
        const res = await axios.get(this.$root.apiHost + `api/hr_rapor/${id}`);

        this.todo = {
          id: id,
          profiles_id: res.data.data.profiles_id,
          grooming: res.data.data.grooming,
          pk: res.data.data.pk,
          soba: res.data.data.soba,
          disiplin: res.data.data.disiplin,
          admin: res.data.data.admin,
          mop: res.data.data.mop,
          yop: res.data.data.yop,
        };

        // Get profile dan jabatan data
        const profile = this.profiles.find(
          (p) => p.profile_name === res.data.data.profiles_id
        );
        if (profile) {
          this.selectedProfile = {
            profile_name: profile.profile_name,
            id: profile.id,
          };

          // Get active kriteria based on jabatan
          const profileData = await axios.get(
            this.$root.apiHost + `api/profiles/${profile.id}`
          );

          if (profileData.data.data?.jabatan_id) {
            await this.getActiveKriteria(profileData.data.data.jabatan_id);
          }
        }
      } catch (error) {
        toast.error(error.response?.data?.message || "Error fetching data", {
          theme: "colored",
        });
      } finally {
        this.$root.stopLoading();
      }
    },
  },
};
</script>

<style scoped>
.input-error {
  border: red 1px solid;
}
.modal-dialog2 {
  width: 80%;
  margin: 30px auto;
}

:deep(.v-select) {
  background-color: white;
  border-radius: 4px;
  font-size: 14px;
  z-index: 1030;
}

.modal {
  z-index: 1050 !important;
}

.modal-backdrop {
  z-index: 1040 !important;
}

.form-control[readonly] {
  background-color: #e9ecef;
  opacity: 1;
  cursor: not-allowed;
}
</style>
