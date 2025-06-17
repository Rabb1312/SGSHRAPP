<template>
  <!---------------------------- Modal -->
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
          <!-- <pre>{{ todo }}</pre> -->
          <!-- Wizards Row -->
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-nf-email">Kode Bank</label>
                  <CmpInputText
                    id="inputA"
                    type="text"
                    placeholder="Kode Bank"
                    v-model="todo.bank_code"
                    :class="
                      errorField.bank_code
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                    @input="
                      (val) => (todo.bank_code = todo.bank_code.toUpperCase())
                    "
                    :disabled="!flagButtonAdd"
                  />
                </div>
              </div>
              <!-- :disabled="!flagButtonAdd" -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-nf-email">Nama Bank</label>
                  <CmpInputText
                    type="text"
                    placeholder="Nama Bank"
                    v-model="todo.bank_name"
                    :class="
                      errorField.bank_name
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                    @input="
                      (val) => (todo.bank_name = todo.bank_name.toUpperCase())
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
                  :disabled="
                    $root.flagButtonLoading ||
                    todo.bank_code == null ||
                    todo.bank_code == '' ||
                    todo.bank_name == null ||
                    todo.bank_name == ''
                  "
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
                  :disabled="
                    $root.flagButtonLoading ||
                    todo.bank_code == null ||
                    todo.bank_code == '' ||
                    todo.bank_name == null ||
                    todo.bank_name == ''
                  "
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

        <!-- <download-excel
          class="button"
          :data="json_data"
          :fields="json_fields"
          :worksheet="nama_sheetnya"
          :before-generate="startDownload"
          :before-finish="finishDownload"
        >
          <button
            class="btn btn-sm btn-info pull-left"
            @click="download_excel_xyz()"
          >
            <i class="class fas fa-file-excel"></i>
            Export Excel
          </button>
        </download-excel> -->

        <!-- <button class="btn btn-sm btn-danger pull-left" @click="exportPdf()">
          Export PDF
        </button> -->

        <button
          v-if="status_table && $root.accessRoles[access_page].create"
          class="btn btn-sm btn-primary pull-right"
          @click="show_modal()"
        >
          ADD DATA
        </button>

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

import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";

export default {
  components: {
    downloadExcel: JsonExcel,
    mounted() {
      const link = document.createElement("link");
      link.rel = "stylesheet";
      link.href =
        "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css";
      document.head.appendChild(link);
    },
    // CmpSelect2,
    // LoadingX,
    // CmpInputText,
    // CmpInputText,
  },
  data() {
    return {
      access_page: this.$root.decryptData(localStorage.getItem("halaman")),
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      userid: this.$root.get_id_user(),
      activemenu: null,
      grid: new Grid(),
      // grid2: new Grid(),
      errorField: {
        bank_code: false,
        bank_name: false,
      },

      userid: 0,
      status_table: false,

      modal: false,

      todo: {
        bank_code: "",
        bank_name: "",
      },

      flagButtonAdd: true,

      data_x_table: [],
      data_x_excel: [],
      nama_excelnya: "",
      nama_sheetnya: "",
      json_data: [],
      json_meta: [[{ key: "charset", value: "UTF-8" }]],
      json_fields: {
        bank_code: "bank_code", //sesuaikan database yang diperlukan
        bank_name: "bank_name",
      },
    };
  },

  computed: {
    // Add computed property for userid
    currentUserId() {
      return this.userid || this.$root.get_id_user();
    },
  },

  async mounted() {
    console.log(
      "Setting userid:",
      this.$root.get_id_user(localStorage.getItem("unique"))
    );
    this.userid = this.$root.get_id_user(localStorage.getItem("unique"));

    await this.getTable();
  },
  methods: {
    async exportPdf() {
      const mythis = this;
      mythis.$root.presentLoading();

      try {
        let allData = [];
        let count = 1;
        let nn = 0;
        const limitx = 100;

        while (count > 0) {
          const offsetx = limitx * nn;

          const reqData = await axios({
            method: "get",
            url:
              mythis.$root.apiHost +
              "api/hr_bank?offset=" +
              offsetx +
              "&limit=" +
              limitx,
          });

          const resData = reqData.data;
          allData = [...allData, ...resData.results];

          if (resData.results.length === 0 || resData.results.length < limitx) {
            count = 0;
          }

          nn++;
          if (nn >= 100) {
            // Safety check to prevent infinite loop
            count = 0;
          }
        }

        const doc = new jsPDF();
        let totalPagesExp = "{total_pages_count_string}";

        doc.setFontSize(18);
        doc.text("Master Area Report", 14, 22);
        doc.setFontSize(11);
        doc.setTextColor(100);

        // Add Print Date
        doc.setFontSize(10);
        doc.text(`Print Date: ${new Date().toLocaleString()}`, 14, 30);

        doc.autoTable({
          theme: "striped",
          head: [["No", "Bank Code", "Bank Name", "Created By", "Created At"]],
          body: allData.map((hr_bank, index) => [
            index + 1,
            hr_bank.bank_code,
            hr_bank.bank_name,
            hr_bank.created_by, // TAMBAHKAN DI MODEL created_by
            new Date(hr_bank.created_at).toLocaleString(), // TAMBAHKAN DI MODEL created_at
          ]),
          startY: 35, // Adjusted to accommodate the Print Date
          didDrawPage: function (data) {
            // Footer
            let str = "Page " + doc.internal.getNumberOfPages();
            if (typeof doc.putTotalPages === "function") {
              str = str + " of " + totalPagesExp;
            }
            doc.setFontSize(10);

            let pageSize = doc.internal.pageSize;
            let pageHeight = pageSize.height
              ? pageSize.height
              : pageSize.getHeight();
            doc.text(str, data.settings.margin.left, pageHeight - 10);
          },
          showHead: "everyPage",
        });

        if (typeof doc.putTotalPages === "function") {
          doc.putTotalPages(totalPagesExp);
        }

        const fileName =
          "Master_Bank_Report_" + mythis.formatDate(new Date()) + ".pdf";
        doc.save(fileName);
        console.log(fileName + " generated");

        mythis.$root.stopLoading();
        Swal.fire("Success", "PDF has been generated successfully", "success");
      } catch (error) {
        console.error("Error generating PDF:", error);
        mythis.$root.stopLoading();
        Swal.fire("Error", "Failed to generate PDF", "error");
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
      var mythis = this;
      mythis.$root.presentLoading();
      var nn = 0;
      var count = 1;
      var limitx = 100;
      var offsetx = 0;
      var baris = 0;

      var nomor_x = 1;
      var br_pdf = 0;
      var br_flag = 0;
      var br_string = "";
      var html = "";

      var baris_excel = 0;
      // mythis.json_data = [];
      mythis.data_x_excel = [];

      while (count > 0) {
        offsetx = limitx * nn;

        const reqData = await axios({
          method: "get",
          url:
            mythis.$root.apiHost +
            "api/hr_bank?offset=" +
            offsetx +
            "&limit=" +
            limitx,
        });

        console.log(reqData);

        const resData = reqData.data;
        console.log(resData.results.length);
        if (resData.results.length == 0) {
          count = 0;
        }

        Object.keys(resData.results).forEach(function (key) {
          const countries_x = {
            nomor: nomor_x,

            bank_code: "'" + resData.results[key].bank_code,
            bank_name: resData.results[key].bank_name,
          };
          mythis.data_x_excel[baris_excel] = countries_x;

          br_pdf++;
          baris_excel++;
          nomor_x++;
          ////////////////////////////////////////////////////////
          ////////////////////////////////////////////////////////
        });

        nn = nn + 1;
        if (resData.count < resData.nomorBaris) {
          count = 0;
        }
        if (nn >= 100) {
          count = 0;
        }
      }

      baris_excel++;
      //Penutup Excel

      baris_excel++;
      var countries_x = {
        nomor: "",
        bank_name: "Print Date",
        bank_code: mythis.formatDate(new Date()),
      };
      mythis.data_x_excel[baris_excel] = countries_x;

      mythis.json_data = mythis.data_x_excel;
      mythis.flagDownloadXLS = 1;

      var a = new Date().toLocaleString("en-GB");
      mythis.nama_excelnya = "MASTER_BANK" + a + ".xls";
      mythis.nama_sheetnya = mythis.nama_excelnya;

      mythis.$root.stopLoading();
    },

    download_excel_xyz() {},
    async startDownload() {
      await this.getDataExportExcel();
    },
    finishDownload() {},

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
        title: "Create Data Bank",
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
          var url = mythis.$root.apiHost + "api/hr_bank";
          axios
            .post(
              url,
              {
                bank_code: mythis.todo.bank_code,
                bank_name: mythis.todo.bank_name,
                userid: mythis.currentUserId,
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
                      toast.error(mythis.errorList[key], {
                        theme: "colored",
                        onClose: () => mythis.show_modal(false), // AUTO CLOSE SAAT MODAL ERROR
                        autoClose: 2500,
                      });

                      // const myArray = key.split(".");
                      // mythis.errorField[myArray[1]] = true;
                      mythis.errorField[key] = true;
                    });
                  }
                } else {
                  toast.error(error.response.data.message, {
                    theme: "colored",
                    onClose: () => mythis.show_modal(false), // AUTO CLOSE SAAT MODAL ERROR
                    autoClose: 2500,
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
    },
    getTable() {
      var mythis = this;
      this.grid = new Grid();
      this.grid.updateConfig({
        // language: idID,
        pagination: {
          limit: mythis.$root.pagingTabel1,
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
          "Kode Bank",
          "Nama Bank",
          {
            name: "Action",
            formatter: (_, row) =>
              mythis.$root.accessRoles[mythis.access_page].update &&
              mythis.$root.accessRoles[mythis.access_page].delete
                ? html(
                    `
                    <button data-id="${row.cells[0].data}" class="btn btn-sm btn-warning text-white" id="editData" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil-square-o"></i></button>
                    &nbsp;&nbsp;&nbsp;
                    <button data-id="${row.cells[0].data}" class="btn btn-sm btn-danger text-white" id="deleteData" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash-o"></i></button>
                  `
                  )
                : mythis.$root.accessRoles[mythis.access_page].update
                ? html(
                    `
                    <button data-id="${row.cells[0].data}" class="btn btn-sm btn-warning text-white" id="editData" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil-square-o"></i></button>`
                  )
                : mythis.$root.accessRoles[mythis.access_page].delete
                ? html(`&nbsp;&nbsp;&nbsp;
                    <button data-id="${row.cells[0].data}" class="btn btn-sm btn-danger text-white" id="deleteData" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash-o"></i></button>`)
                : ``,
          },
        ],
        style: {
          table: {
            border: "1px solid #ccc",
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
          url: this.$root.apiHost + "api/hr_bank",
          then: (data) =>
            data.results.map((card) => [
              card.id,
              data.nomorBaris++ + 1,
              html(`<span class="pull-left">${card.bank_code}</span>`),
              html(`<span class="pull-left">${card.bank_name}</span>`),
            ]),
          total: (data) => data.count,
          handle: (res) => {
            // no matching records found
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
              userid: mythis.currentUserId,
            },
          };
          // const config = "";
          axios
            .delete(mythis.$root.apiHost + `api/hr_bank/${id}`, config)
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
          mythis.$root.apiHost + "api/hr_bank/" + mythis.todo.id,
          {
            bank_code: mythis.todo.bank_code,
            bank_name: mythis.todo.bank_name,
            userid: mythis.currentUserId,
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
        .get(mythis.$root.apiHost + `api/hr_bank/${id}`, config)
        .then(async (res) => {
          //console.log(res.data.data);
          //mythis.acuanEdit = id;
          //mythis.todo = res.data.data;
          mythis.todo.id = id;
          mythis.todo.bank_code = res.data.data.bank_code;
          mythis.todo.bank_name = res.data.data.bank_name;

          document.getElementById("inputA").focus(); // sets the focus on the input

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
