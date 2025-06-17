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
                  <label for="example-nf-email">Nama Karyawan</label>
                  <v-select
                    id="profile_id"
                    variant="outlined"
                    label="profile_name"
                    name="profile_name"
                    :options="profiles"
                    placeholder="Pilih Karyawan"
                    v-model="todo.profiles_id"
                    @update:modelValue="onChangeProfile"
                    :disabled="!flagButtonAdd"
                  >
                  </v-select>
                </div>
              </div>
              <!-- :disabled="!flagButtonAdd" -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-nf-email">BPJS Kesehatan</label>
                  <CmpInputText
                    type="text"
                    placeholder="No BPJS"
                    v-model="todo.bpjs_no"
                    :class="
                      errorField.bpjs_no
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                    @input="
                      (val) => (todo.bpjs_no = todo.bpjs_no.toUpperCase())
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
                  <label for="example-nf-email">BPJS Ketenagakerjaan</label>
                  <CmpInputText
                    type="text"
                    placeholder="BPJS Ketenagakerjaan"
                    v-model="todo.bpjs_ket"
                    :class="
                      errorField.bpjs_ket
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                    @input="
                      (val) => (todo.bpjs_ket = todo.bpjs_ket.toUpperCase())
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
                    todo.profiles_id == null ||
                    todo.profiles_id == '' ||
                    todo.bpjs_no == null ||
                    todo.bpjs_no == ''
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
                    todo.profiles_id == null ||
                    todo.profiles_id == '' ||
                    todo.bpjs_no == null ||
                    todo.bpjs_no == ''
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
      access_page: this.$root.decryptData(localStorage.getItem("halaman")),
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      activemenu: null,
      grid: new Grid(),
      // grid2: new Grid(),
      errorField: {
        profiles_id: false,
        bpjs_no: false,
        bpjs_ket: false,
      },

      userid: 0,
      status_table: false,

      modal: false,

      todo: {
        profiles_id: "",
        bpjs_no: "",
        bpjs_ket: "",
      },

      flagButtonAdd: true,

      selectedprofiles: {},
      profiles: [],

      data_x_table: [],
      data_x_excel: [],
      nama_excelnya: "",
      nama_sheetnya: "",
      json_data: [],
      json_meta: [[{ key: "charset", value: "UTF-8" }]],
      json_fields: {
        profiles_id: "profiles_id", //sesuaikan database yang diperlukan
        bpjs_no: "bpjs_no",
        bpjs_ket: "bpjs_ket",
      },
    };
  },
  async mounted() {
    // await this.$root.refreshToken(localStorage.getItem("token"));
    this.getTable();
    this.userid = this.$root.get_id_user(localStorage.getItem("unique"));
    this.getProfile();
  },
  methods: {
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
            "api/hr_bpjs?offset=" +
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

            profiles_id: "'" + resData.results[key].profiles_id,
            bpjs_no: resData.results[key].bpjs_no,
            bpjs_ket: resData.results[key].bpjs_ket,
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
        profiles_id: "Print Date",
        bpjs_no: mythis.formatDate(new Date()),
      };
      mythis.data_x_excel[baris_excel] = countries_x;

      mythis.json_data = mythis.data_x_excel;
      mythis.flagDownloadXLS = 1;

      var a = new Date().toLocaleString("en-GB");
      mythis.nama_excelnya = "MASTER_BPJS" + a + ".xls";
      mythis.nama_sheetnya = mythis.nama_excelnya;

      mythis.$root.stopLoading();
    },

    download_excel_xyz() {},
    async startDownload() {
      await this.getDataExportExcel();
    },
    finishDownload() {},

    onChangeProfile(selectedprofiles) {
      if (selectedprofiles) {
        this.todo.profiles_id = selectedprofiles.profile_name;
      } else {
        this.todo.profiles_id = "";
      }
    },
    async getProfile() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/profilesalldata"
        );
        this.profiles = response.data;

        if (this.profiles.length > 0) {
          this.selectedjabatan = this.profiles[0];
        } else {
          this.selectedjabatan = null;
        }
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

      try {
        const box = document.getElementById("box");
        if (!box) {
          console.error("Box container not found");
          return;
        }

        // Remove old wrapper if exists
        const oldWrapper = document.getElementById("wrapper2");
        if (oldWrapper) {
          oldWrapper.remove();
        }

        // Create new wrapper
        const newWrapper = document.createElement("div");
        newWrapper.id = "wrapper2";
        box.appendChild(newWrapper);

        // Initialize table
        this.getTable();
      } catch (err) {
        console.error("Error refreshing table:", err);
      }
    },
    saveTodo() {
      var mythis = this;

      Swal.fire({
        title: "Create Data BPJS",
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
          var url = mythis.$root.apiHost + "api/hr_bpjs";
          axios
            .post(
              url,
              {
                profiles_id: mythis.todo.profiles_id,
                bpjs_no: mythis.todo.bpjs_no,
                bpjs_ket: mythis.todo.bpjs_ket,
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
    },
    getTable() {
      var mythis = this;
      this.grid = new Grid();
      this.grid.updateConfig({
        pagination: {
          limit: mythis.$root.pagingTabel1,
          server: {
            url: (prev, page, limit) => {
              // Pastikan URL terbentuk dengan benar
              const baseUrl = mythis.$root.apiHost + "api/hr_bpjs";
              return `${baseUrl}?limit=${limit}&offset=${page * limit}`;
            },
          },
        },
        search: {
          server: {
            url: (prev, keyword) => {
              const baseUrl = mythis.$root.apiHost + "api/hr_bpjs";
              return `${baseUrl}?search=${keyword}`;
            },
          },
        },
        columns: [
          { name: "ID", hidden: true },
          "No",
          "Nama Karyawan",
          "BPJS Kesehatan",
          "BPJS Ketenagakerjaan",
          {
            name: "Action",
            formatter: (_, row) => {
              const id = row.cells[0].data;
              const hasUpdate =
                mythis.$root.accessRoles[mythis.access_page].update;
              const hasDelete =
                mythis.$root.accessRoles[mythis.access_page].delete;

              let buttons = "";

              if (hasUpdate) {
                buttons += `<button data-id="${id}" class="btn btn-sm btn-warning text-white" id="editData">
              <i class="fa fa-pencil-square-o"></i>
            </button>`;
              }

              if (hasDelete) {
                buttons += `&nbsp;&nbsp;&nbsp;<button data-id="${id}" class="btn btn-sm btn-danger text-white" id="deleteData">
              <i class="fa fa-trash-o"></i>
            </button>`;
              }

              return html(buttons);
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
          url: mythis.$root.apiHost + "api/hr_bpjs",
          then: (data) => {
            // Tambahkan validasi data
            if (!data || !data.results) {
              console.error("Invalid response data:", data);
              return [];
            }

            try {
              return data.results.map((card, index) => [
                card.id,
                (data.offset || 0) + index + 1,
                card.profiles_id || "",
                card.bpjs_no || "",
                card.bpjs_ket || "",
              ]);
            } catch (err) {
              console.error("Error processing data:", err);
              return [];
            }
          },
          total: (data) => {
            return data?.count || 0;
          },
          handle: (response) => {
            if (!response.ok) {
              console.error("Server error:", response.status);
              return { results: [], count: 0 };
            }
            return response.json();
          },
        },
      });

      // Pastikan element wrapper ada sebelum render
      const wrapper = document.getElementById("wrapper2");
      if (wrapper) {
        try {
          this.grid.render(wrapper);
        } catch (err) {
          console.error("Error rendering grid:", err);
        }
      } else {
        console.error("Grid wrapper element not found");
      }

      // Reset event handlers
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
            .delete(mythis.$root.apiHost + `api/hr_bpjs/${id}`, config)
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
          mythis.$root.apiHost + "api/hr_bpjs/" + mythis.todo.id,
          {
            profiles_id: mythis.todo.profiles_id,
            bpjs_no: mythis.todo.bpjs_no,
            bpjs_ket: mythis.todo.bpjs_ket,
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
        .get(mythis.$root.apiHost + `api/hr_bpjs/${id}`, config)
        .then(async (res) => {
          //console.log(res.data.data);
          //mythis.acuanEdit = id;
          //mythis.todo = res.data.data;
          mythis.todo.id = id;
          mythis.todo.profiles_id = res.data.data.profiles_id;
          mythis.todo.bpjs_no = res.data.data.bpjs_no;
          mythis.todo.bpjs_ket = res.data.data.bpjs_ket;

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
