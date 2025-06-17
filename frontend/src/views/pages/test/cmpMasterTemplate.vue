div
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
          <pre>{{ todo }}</pre>
          <!-- Wizards Row -->
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-nf-email">Doc Name</label>
                  <CmpInputText
                    id="inputA"
                    type="text"
                    placeholder="Doc Name"
                    v-model="todo.doc_name"
                    :class="
                      errorField.doc_name
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                    @input="
                      (val) => (todo.doc_name = todo.doc_name.toUpperCase())
                    "
                    :disabled="!flagButtonAdd"
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-nf-email">Doc Desc</label>
                  <CmpInputText
                    type="text"
                    placeholder="Doc Desc"
                    v-model="todo.doc_desc"
                    :class="
                      errorField.doc_desc
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                    @input="
                      (val) => (todo.doc_desc = todo.doc_desc.toUpperCase())
                    "
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-nf-email">Template Name</label>
                  <CmpInputText
                    id="inputA"
                    type="text"
                    placeholder="Template Name"
                    v-model="todo.template_name"
                    :class="
                      errorField.template_name
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                    @input="
                      (val) =>
                        (todo.template_name = todo.template_name.toUpperCase())
                    "
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-nf-email">Total Input</label>
                  <CmpInputText
                    id="inputA"
                    type="number"
                    placeholder="Total Input"
                    v-model="todo.total_input"
                    :class="
                      errorField.total_input
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                    @input="generateDynamicInputs"
                  />
                </div>
              </div>
              <!-- :disabled="!flagButtonAdd" -->
            </div>
          </div>

          <!-- Header Mapping, tampil hanya jika ada input dinamis -->
          <div v-if="mappingInputs.length > 0" class="header-mapping">
            <h4><strong>Mapping</strong></h4>
          </div>

          <!-- Input Dinamis -->
          <div v-for="(input, index) in mappingInputs" :key="index" class="row">
            <!-- Input Name -->
            <div class="col-md-4">
              <div class="form-group">
                <label>Input Name</label>
                <CmpInputText
                  v-model="input.input_name"
                  placeholder="Input Name"
                  @input="input.input_name = input.input_name.toUpperCase()"
                />
              </div>
            </div>

            <!-- Mapping Number -->
            <div class="col-md-4">
              <div class="form-group">
                <label>Mapping Number</label>
                <CmpInputText
                  v-model="input.mapping_number"
                  type="number"
                  placeholder="Mapping Number"
                />
              </div>
            </div>

            <!-- Input Type (Dropdown) -->
            <div class="col-md-4">
              <div class="form-group">
                <label>Input Type</label>
                <select
                  v-model="input.input_type"
                  class="form-control input-lg"
                >
                  <option value="text">Text</option>
                  <option value="text-area">Text Area</option>
                </select>
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
                    todo.doc_name == null ||
                    todo.doc_name == '' ||
                    todo.doc_desc == null ||
                    todo.doc_desc == '' ||
                    todo.template_name == null ||
                    todo.template_name == '' ||
                    todo.total_input == null ||
                    todo.total_input == ''
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
                    todo.doc_name == null ||
                    todo.doc_name == '' ||
                    todo.doc_desc == null ||
                    todo.doc_desc == '' ||
                    todo.template_name == null ||
                    todo.template_name == '' ||
                    todo.total_input == null ||
                    todo.total_input == ''
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
          type="xlsx"
        >
          <button
            class="btn btn-sm btn-info pull-left"
            @click="download_excel_xyz()"
          >
            <i class="class fas fa-file-excel"></i>
            Export Excel
          </button>
        </download-excel> -->

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

import VueDocumentEditor from "vue-document-editor";

export default {
  components: {
    VueDocumentEditor,
    downloadExcel: JsonExcel,
    // CmpSelect2,
    // LoadingX,
    // CmpInputText,
    // CmpInputText,
  },
  data() {
    return {
      access_page: this.$root.decryptData(localStorage.getItem("halaman")),
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      activemenu: null,
      grid: new Grid(),
      // grid2: new Grid(),
      errorField: {
        doc_name: false,
        doc_desc: false,
        template_name: false,
        total_input: false,
      },

      userid: 0,
      status_table: false,

      modal: false,

      todo: {
        doc_name: "",
        doc_desc: "",
        template_name: "",
        total_input: "",
      },

      flagButtonAdd: true,

      data_x_table: [],
      data_x_excel: [],
      nama_excelnya: "",
      nama_sheetnya: "",
      json_data: [],
      json_meta: [[{ key: "charset", value: "UTF-8" }]],
      json_fields: {
        unit_number: "unit_number", //sesuaikan database yang diperlukan
        unit_code: "unit_code",
        unit_name: "unit_name",
      },
      mappingInputs: [],
    };
  },
  async mounted() {
    // await this.$root.refreshToken(localStorage.getItem("token"));
    this.getTable();
    this.userid = this.$root.get_id_user(localStorage.getItem("unique"));
  },
  watch: {
    "todo.total_input"(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.generateDynamicInputs();
      }
    },
  },
  methods: {
    generateDynamicInputs() {
      const currentLength = this.mappingInputs.length;
      const newLength = parseInt(this.todo.total_input);

      // Jika total input bertambah
      if (newLength > currentLength) {
        for (let i = currentLength; i < newLength; i++) {
          this.mappingInputs.push({
            input_name: "",
            mapping_number: "",
            input_type: "",
          });
        }
      }
      // Jika total input berkurang
      else if (newLength < currentLength) {
        this.mappingInputs.splice(newLength);
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
            "api/doc_list?offset=" +
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

            doc_name: "'" + resData.results[key].doc_name,
            doc_desc: resData.results[key].doc_desc,
            template_name: resData.results[key].template_name,
            total_input: resData.results[key].total_input,
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
        doc_name: "Print Date",
        doc_desc: mythis.formatDate(new Date()),
      };
      mythis.data_x_excel[baris_excel] = countries_x;

      mythis.json_data = mythis.data_x_excel;
      mythis.flagDownloadXLS = 1;

      var a = new Date().toLocaleString("en-GB");
      mythis.nama_excelnya = "TEMPLATE" + a + ".xlsx";
      // mythis.nama_sheetnya = mythis.nama_excelnya;

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

      // Reset errorField
      Object.keys(mythis.errorField).forEach(function (key) {
        mythis.errorField[key] = false;
      });

      // Reset data form
      mythis.todo = {
        doc_name: "",
        doc_desc: "",
        template_name: "",
        total_input: "",
      };

      // Reset input dinamis
      mythis.mappingInputs = [];

      // Reset errorList
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
        title: "Create Data Template",
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

          const config = "";

          // Kumpulkan data input dinamis
          const dynamicInputs = mythis.mappingInputs.map((input) => ({
            input_name: input.input_name,
            mapping_number: input.mapping_number,
            input_type: input.input_type,
          }));

          console.log(dynamicInputs); // Pastikan array dynamicInputs terisi dengan benar

          var url = mythis.$root.apiHost + "api/doc_list";

          // Kirim data utama dan data dinamis dalam satu request
          axios
            .post(
              url,
              {
                doc_name: mythis.todo.doc_name,
                doc_desc: mythis.todo.doc_desc,
                template_name: mythis.todo.template_name,
                total_input: mythis.todo.total_input,
                userid: mythis.userid,
                dynamic_inputs: dynamicInputs, // Tambahkan data dinamis ke payload
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
              // mythis.$root.sendNotifFirebase("Notifikasi Pesan", "Created Successfully");
            })
            .catch(function (error) {
              mythis.$root.flagButtonLoading = false;
              mythis.$root.stopLoading();
              if (error.response) {
                if (error.response.status == 422) {
                  mythis.errorList = error.response.data;
                  if (Object.keys(mythis.errorList).length > 0) {
                    // Reset semua error field menjadi false
                    Object.keys(mythis.errorField).forEach(function (key) {
                      mythis.errorField[key] = false;
                    });
                    // Tampilkan error di field yang relevan
                    Object.keys(mythis.errorList).forEach(function (key) {
                      toast.error(mythis.errorList[key], {
                        theme: "colored",
                        onClose: () => mythis.show_modal(false), // AUTO CLOSE SAAT MODAL ERROR
                        autoClose: 2500,
                      });
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
          "Doc Name",
          "Doc Desc",
          "Template Name",
          "Total Input",
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
          url: this.$root.apiHost + "api/doc_list",
          then: (data) =>
            data.results.map((card) => [
              card.id,
              data.nomorBaris++ + 1,
              html(`<span class="pull-left">${card.doc_name}</span>`),
              html(`<span class="pull-left">${card.doc_desc}</span>`),
              html(`<span class="pull-left">${card.template_name}</span>`),
              html(`<span class="pull-left">${card.total_input}</span>`),
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
              userid: mythis.userid,
            },
          };
          // const config = "";
          axios
            .delete(mythis.$root.apiHost + `api/doc_list/${id}`, config)
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
          mythis.$root.apiHost + "api/doc_list/" + mythis.todo.id,
          {
            doc_name: mythis.todo.doc_name,
            doc_desc: mythis.todo.doc_desc,
            template_name: mythis.todo.template_name,
            total_input: mythis.todo.total_input,
            userid: mythis.userid,
            dynamic_inputs: mythis.mappingInputs,
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
        .get(mythis.$root.apiHost + `api/doc_list/${id}`, config)
        .then(async (res) => {
          //console.log(res.data.data);
          //mythis.acuanEdit = id;
          //mythis.todo = res.data.data;
          mythis.todo.id = id;
          mythis.todo.doc_name = res.data.data.doc_name;
          mythis.todo.doc_desc = res.data.data.doc_desc;
          mythis.todo.template_name = res.data.data.template_name;
          mythis.todo.total_input = res.data.data.total_input;

          // Panggil generateDynamicInputs untuk memperbarui input dinamis
          this.generateDynamicInputs();

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
