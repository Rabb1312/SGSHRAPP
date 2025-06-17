<template>
  <!---------------------------- Modal -->
  <!-- Add Modal -->
  <div
    :class="modalAdd ? 'modal fade in' : 'modal fade'"
    id="addModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="addModalTitle"
    aria-hidden="true"
    data-keyboard="false"
    data-backdrop="static"
    :style="{ display: modalAdd ? 'block' : 'none' }"
  >
    <div class="modal-dialog2 modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          TAMBAH DATA {{ $root.judulHalaman }}
          <button
            type="button"
            class="close"
            @click="closeAddModal"
            aria-label="Close"
          >
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-group">
                <label>Cabang</label>
                <v-select
                  variant="outlined"
                  label="nama_cabang"
                  :options="cabang_list"
                  placeholder="Pilih Cabang"
                  v-model="selectedcabangs"
                  :get-option-label="(option) => option.nama_cabang"
                  :reduce="(option) => option.nama_cabang"
                  @update:modelValue="onChangeCabang"
                ></v-select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Karyawan</label>
                <v-select
                  variant="outlined"
                  label="profile_name"
                  :options="profiles"
                  placeholder="Pilih Karyawan"
                  v-model="selectedProfile"
                  :reduce="(option) => option.profile_name"
                  :disabled="!selectedcabangs"
                >
                </v-select>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-group">
                <label>Bulan</label>
                <v-select
                  variant="outlined"
                  :options="months"
                  placeholder="Pilih Bulan"
                  v-model="selectedMonth"
                  :reduce="(option) => option.value"
                ></v-select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Tahun</label>
                <v-select
                  variant="outlined"
                  :options="years"
                  placeholder="Pilih Tahun"
                  v-model="selectedYear"
                  :reduce="(option) => option.value"
                ></v-select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Toko</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="newTodo.nama_toko"
                  :class="{ 'input-error': errorField.nama_toko }"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Channel</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="newTodo.channel"
                  :class="{ 'input-error': errorField.channel }"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Kriteria BMI</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  readonly
                  placeholder="18.5 - 25 = Normal"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>TB</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="newTodo.tb"
                  :class="{ 'input-error': errorField.tb }"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>BB</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="newTodo.bb"
                  :class="{ 'input-error': errorField.bb }"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>BMI</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="newTodo.bmi"
                  readonly
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Klasifikasi</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="newTodo.klasifikasi"
                  readonly
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>PK</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="newTodo.pk"
                  :class="{ 'input-error': errorField.pk }"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Target Siba</label>
                <input
                  type="number"
                  class="form-control input-lg"
                  v-model="newTodo.target_siba"
                  :class="{ 'input-error': errorField.target_siba }"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Target Soba</label>
                <input
                  type="number"
                  class="form-control input-lg"
                  v-model="newTodo.target_soba"
                  :class="{ 'input-error': errorField.target_soba }"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Siba</label>
                <input
                  type="number"
                  class="form-control input-lg"
                  v-model="newTodo.siba"
                  :class="{ 'input-error': errorField.siba }"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Soba</label>
                <input
                  type="number"
                  class="form-control input-lg"
                  v-model="newTodo.soba"
                  :class="{ 'input-error': errorField.soba }"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Achv Siba</label>
                <input
                  type="number"
                  class="form-control input-lg"
                  v-model="newTodo.achv_siba"
                  :class="{ 'input-error': errorField.achv_siba }"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Achv Soba</label>
                <input
                  type="number"
                  class="form-control input-lg"
                  v-model="newTodo.achv_soba"
                  :class="{ 'input-error': errorField.achv_soba }"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-sm btn-primary"
            @click="saveTodo"
            :disabled="$root.flagButtonLoading"
          >
            <i
              v-if="$root.flagButtonLoading"
              class="fa fa-spinner fa-spin text-default"
            ></i>
            Simpan
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal Edit -->
  <div
    :class="modal ? 'modal fade in' : 'modal fade'"
    id="editModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="editModalTitle"
    aria-hidden="true"
    data-keyboard="false"
    data-backdrop="static"
    :style="{ display: modal ? 'block' : 'none' }"
  >
    <div class="modal-dialog2 modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          EDIT DATA {{ $root.judulHalaman }}
          <button
            type="button"
            class="close"
            @click="show_modal(false)"
            aria-label="Close"
          >
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-group">
                <label>Cabang</label>
                <v-select
                  variant="outlined"
                  label="nama_cabang"
                  :options="cabang_list"
                  placeholder="Pilih Cabang"
                  v-model="selectedcabangs"
                  :get-option-label="(option) => option.nama_cabang"
                  :reduce="(option) => option.nama_cabang"
                  :disabled="isReadonly"
                ></v-select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Karyawan</label>
                <v-select
                  variant="outlined"
                  label="profile_name"
                  :options="profiles"
                  placeholder="Pilih Karyawan"
                  v-model="selectedProfile"
                  :reduce="(option) => option.profile_name"
                  :disabled="isReadonly"
                >
                </v-select>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-group">
                <label>Bulan</label>
                <v-select
                  variant="outlined"
                  :options="months"
                  placeholder="Pilih Bulan"
                  v-model="selectedMonth"
                  :reduce="(option) => option.value"
                  :disabled="isReadonly"
                ></v-select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Tahun</label>
                <v-select
                  variant="outlined"
                  :options="years"
                  placeholder="Pilih Tahun"
                  v-model="selectedYear"
                  :reduce="(option) => option.value"
                  :disabled="isReadonly"
                ></v-select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nama Toko</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.nama_toko"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Channel</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.channel"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Kriteria BMI</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  readonly
                  placeholder="18.5 - 25 = Normal"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>TB</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.tb"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>BB</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.bb"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>BMI</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.bmi"
                  readonly
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Klasifikasi</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.klasifikasi"
                  readonly
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>PK</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.pk"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Target Siba</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.target_siba"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Target Soba</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.target_soba"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Siba</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.siba"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Soba</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.soba"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Achv Siba</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.achv_siba"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Achv Soba</label>
                <input
                  type="text"
                  class="form-control input-lg"
                  v-model="todo.achv_soba"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-sm btn-primary"
            @click="editTodo"
            :disabled="$root.flagButtonLoading"
          >
            <i
              v-if="$root.flagButtonLoading"
              class="fa fa-spinner fa-spin text-default"
            ></i>
            Update
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Sebelum csv import -->
  <div v-if="csv && selectedcabangs && selectedProfile" class="row mb-3">
    <div class="col-md-12">
      <div class="form-group">
        <label><strong>Preview Data:</strong></label>
        <!-- <pre
          style="
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            max-height: 300px;
            overflow-y: auto;
          "
        >
Cabang: {{ cabang_list.find((c) => c.id === selectedcabangs)?.nama_cabang }}
Nama BA: {{ profiles.find((p) => p.id === selectedProfile)?.profile_name }}
Join Date: {{ profiles.find((p) => p.id === selectedProfile)?.join_date }}

Data CSV yang akan disimpan:
{{
            csv
              ? csv.map((row) => ({
                  nama_toko: row.nama_toko,
                  tb: row.tb,
                  bb: row.bb,
                  klasifikasi: row.klasifikasi,
                  pk: row.pk,
                  target_siba: row.target_siba,
                  target_soba: row.target_soba,
                  siba: row.siba,
                  soba: row.soba,
                  achv_siba: row.achv_siba,
                  achv_soba: row.achv_soba,
                }))
              : []
          }}
      </pre
        > -->
      </div>
    </div>
  </div>

  <!-- <div
    :class="modalCsv ? 'modal fade in' : 'modal fade'"
    id="csvImportModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="csvImportModalTitle"
    aria-hidden="true"
    data-keyboard="false"
    data-backdrop="true"
    :style="modalCsv ? 'display: block;' : 'display: none;'"
  >
    <div class="modal-dialog2 modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          IMPORT CSV {{ $root.judulHalaman }}
          <button
            id="closeModal"
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
            :disabled="$root.flagButtonLoading"
            @click="toggleModalCsv()"
          >
            <span aria-hidden="true">X</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-group">
                <label>Cabang</label>
                <v-select
                  variant="outlined"
                  label="nama_cabang"
                  :options="cabang_list"
                  placeholder="Pilih Cabang"
                  v-model="selectedcabangs"
                  :get-option-label="(option) => option.nama_cabang"
                  :reduce="(option) => option.id"
                  @update:modelValue="onChangeCabang"
                ></v-select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Karyawan</label>
                <v-select
                  variant="outlined"
                  label="profile_name"
                  :options="profiles"
                  placeholder="Pilih Karyawan"
                  v-model="selectedProfile"
                  :reduce="(option) => option.id"
                  :disabled="!selectedcabangs"
                >
                </v-select>
              </div>
            </div>
          </div>

          
          <div class="row mb-3">
            
            <div class="col-md-6">
              <div class="form-group">
                <label>Bulan</label>
                <v-select
                  variant="outlined"
                  :options="months"
                  placeholder="Pilih Bulan"
                  v-model="selectedMonth"
                  :reduce="(option) => option.value"
                ></v-select>
              </div>
            </div>

            
            <div class="col-md-6">
              <div class="form-group">
                <label>Tahun</label>
                <v-select
                  variant="outlined"
                  :options="years"
                  placeholder="Pilih Tahun"
                  v-model="selectedYear"
                  :reduce="(option) => option.value"
                ></v-select>
              </div>
            </div>
          </div>

          <vue-csv-import v-model="csv" :fields="dataImportCsv">
            <vue-csv-toggle-headers></vue-csv-toggle-headers>
            <vue-csv-errors></vue-csv-errors>
            <vue-csv-input></vue-csv-input>
            <vue-csv-table-map
              :auto-match="true"
              :table-attributes="{
                id: 'csv-table',
                class: 'table table-bordered table-hover',
              }"
            ></vue-csv-table-map>
          </vue-csv-import>

          <div class="modal-footer">
            <div class="form-group form-actions">
              <div class="col-xs-12">
                <button
                  v-if="csv != null"
                  @click="saveTodoBulky()"
                  type="button"
                  class="btn btn-sm btn-primary pull-left"
                  :disabled="$root.flagButtonLoading"
                >
                  <i
                    v-if="$root.flagButtonLoading"
                    class="fa fa-spinner fa-spin text-default"
                  ></i>
                  SAVE DATA BULKY
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> -->

  <!---------------------------- Modal -->
  <div
    :class="modalCsv ? 'modal fade in' : 'modal fade'"
    id="csvImportModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="csvImportModalTitle"
    aria-hidden="true"
    data-keyboard="false"
    data-backdrop="true"
    :style="modalCsv ? 'display: block;' : 'display: none;'"
  >
    <div class="modal-dialog2 modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          IMPORT CSV {{ $root.judulHalaman }}
          <button
            id="closeModal"
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
            :disabled="$root.flagButtonLoading"
            @click="toggleModalCsv()"
          >
            <span aria-hidden="true">X</span>
          </button>
        </div>
        <div class="modal-body">
          <vue-csv-import v-model="csv" :fields="dataImportCsv">
            <vue-csv-toggle-headers></vue-csv-toggle-headers>
            <vue-csv-errors></vue-csv-errors>
            <vue-csv-input></vue-csv-input>
            <vue-csv-table-map
              :auto-match="true"
              :table-attributes="{
                id: 'csv-table',
                class: 'table table-bordered table-hover',
              }"
            ></vue-csv-table-map>
          </vue-csv-import>

          <div class="modal-footer">
            <div class="form-group form-actions">
              <div class="col-xs-12">
                <button
                  v-if="csv != null"
                  @click="saveTodoBulky()"
                  type="button"
                  class="btn btn-sm btn-primary pull-left"
                  :disabled="$root.flagButtonLoading"
                >
                  <i
                    v-if="$root.flagButtonLoading"
                    class="fa fa-spinner fa-spin text-default"
                  ></i>
                  SAVE DATA BULKY
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Page content -->
  <div id="page-content" v-if="isLogin == 1" style="min-height: 850px">
    <div class="block">
      <div class="block-title">
        <h2>
          <strong>Upload POS {{ $root.judulHalaman }}</strong>
        </h2>
        <i v-if="!status_table" class="fa fa-spinner fa-spin text-default"></i>
      </div>

      <div class="block-content">
        <!-- <button
          v-if="status_table && $root.accessRoles[access_page].create"
          class="btn btn-sm btn-primary pull-right"
          @click="show_modal()"
        >
          ADD DATA
        </button> -->

        <button
          v-if="status_table && $root.accessRoles[access_page].create"
          class="btn btn-sm btn-primary pull-right"
          @click="showAddModal"
        >
          <i class="fa fa-plus"></i> Add Data
        </button>

        <button
          @click="toggleModalCsv()"
          class="btn btn-sm btn-info pull-right"
          style="margin-right: 8px"
        >
          <i class="fa fa-upload"></i> Import CSV
        </button>

        <!-- <pre> -->
        <!-- <vue-csv-import v-model="csv" :fields="dataImportCsv">
          <vue-csv-toggle-headers></vue-csv-toggle-headers>
          <vue-csv-errors></vue-csv-errors>
          <vue-csv-input></vue-csv-input>
          <vue-csv-table-map
            :auto-match="true"
            :table-attributes="{
              id: 'csv-table',
              class: 'table table-bordered table-hover',
            }"
          ></vue-csv-table-map>
        </vue-csv-import> -->
        <!-- </pre> -->
        <!-- <button
          v-if="csv != null"
          @click="saveTodoBulky()"
          type="button"
          class="btn btn-sm btn-primary pull-left"
        >
          SAVE DATA BULKY
        </button> -->

        <div id="wrapper2"></div>
        <div id="box"></div>

        <div class="row mt-3">
          <div
            class="col-md-12 d-flex justify-content-between align-items-center"
          >
            <div class="d-flex align-items-center">
              <span style="margin-right: 10px">Rows per page:</span>
              <select
                v-model="rowsPerPage"
                class="form-control form-control-sm"
                style="width: auto"
                @change="onRowsPerPageChange"
              >
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
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
import {
  VueCsvToggleHeaders,
  VueCsvSubmit,
  VueCsvMap,
  VueCsvInput,
  VueCsvErrors,
  VueCsvImport,
} from "vue-csv-import";
import { readonly } from "vue";

export default {
  components: {
    VueCsvToggleHeaders,
    VueCsvSubmit,
    VueCsvMap,
    VueCsvInput,
    VueCsvErrors,
    VueCsvImport,
    vSelect,
    // CmpSelect2,
    // LoadingX,
    // CmpInputText,
    // CmpInputText,
  },
  data() {
    return {
      access_page: this.$root.decryptData(localStorage.getItem("halaman")),
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      grid: new Grid(),

      selectedMonth: null,
      selectedYear: null,
      months: [
        { label: "Januari", value: "01" },
        { label: "Februari", value: "02" },
        { label: "Maret", value: "03" },
        { label: "April", value: "04" },
        { label: "Mei", value: "05" },
        { label: "Juni", value: "06" },
        { label: "Juli", value: "07" },
        { label: "Agustus", value: "08" },
        { label: "September", value: "09" },
        { label: "Oktober", value: "10" },
        { label: "November", value: "11" },
        { label: "Desember", value: "12" },
      ],
      years: Array.from({ length: 5 }, (_, i) => ({
        label: String(new Date().getFullYear() - 2 + i),
        value: String(new Date().getFullYear() - 2 + i),
      })),

      isReadonly: false,

      selectedcabangs: null,
      selectedProfile: null,
      cabang_list: [],
      profiles: [],

      rowsPerPage: 10,
      startRow: 0,
      endRow: 0,
      totalRows: 0,

      modalAdd: false,
      newTodo: {
        nama_toko: "",
        channel: "",
        tb: "",
        bb: "",
        bmi: "",
        klasifikasi: "",
        pk: "",
        target_siba: "",
        target_soba: "",
        siba: "",
        soba: "",
        achv_siba: "",
        achv_soba: "",
      },

      csv: null,
      errorField: {
        nama_toko: false,
        channel: false,
        tb: false,
        bb: false,
        klasifikasi: false,
        pk: false,
        target_siba: false,
        target_soba: false,
        siba: false,
        soba: false,
        achv_siba: false,
        achv_soba: false,
      },

      userid: 0,
      status_table: false,
      modal: false,
      modalCsv: false,

      dataImportCsv: {
        cabang_id: {
          label: "cabang_id",
          required: true,
        },
        profiles_id: {
          label: "profiles_id",
          required: true,
        },
        mop: {
          label: "mop",
          required: true,
        },
        yop: {
          label: "yop",
          required: true,
        },
        nama_toko: {
          label: "nama_toko",
          required: true,
        },
        channel: {
          label: "channel",
          required: true,
        },
        tb: {
          label: "tb",
          required: true,
        },
        bb: {
          label: "bb",
          required: true,
        },
        klasifikasi: {
          label: "klasifikasi",
          required: true,
        },
        pk: {
          label: "pk",
          required: true,
        },
        target_siba: {
          label: "target_siba",
          required: true,
        },
        target_soba: {
          label: "target_soba",
          required: true,
        },
        siba: {
          label: "siba",
          required: true,
        },
        soba: {
          label: "soba",
          required: true,
        },
        achv_siba: {
          label: "achv_siba",
          required: true,
        },
        achv_soba: {
          label: "achv_soba",
          required: true,
        },
      },

      todo: {
        nama_toko: "",
        channel: "",
        tb: "",
        bb: "",
        bmi: "",
        klasifikasi: "",
        pk: "",
        target_siba: "",
        target_soba: "",
        siba: "",
        soba: "",
        achv_siba: "",
        achv_soba: "",
      },

      flagButtonAdd: true,
      errorList: "",
    };
  },

  mounted() {
    this.getTable();
    this.userid = this.$root.get_id_user(localStorage.getItem("unique"));
    this.loadCabang();
  },

  computed: {
    filteredProfiles() {
      if (!this.selected_cabang) return [];
      return this.profiles_list.filter(
        (p) => p.cabang_id === this.selected_cabang
      );
    },
  },

  watch: {
    rowsPerPage: {
      handler(newValue) {
        this.rowsPerPage = parseInt(newValue);
      },
      immediate: true,
    },
    cabang_list: {
      handler(newVal) {
        console.log("cabang_list updated:", newVal);
      },
      deep: true,
    },
    selectedcabangs(newVal) {
      console.log("selectedcabangs changed:", newVal);
    },
    "todo.tb": {
      handler(newValue) {
        if (newValue && this.todo.bb) {
          const bmiResult = this.calculateBMI(this.todo.bb, newValue);
          this.todo.bmi = bmiResult.bmi.toString();
          this.todo.klasifikasi = bmiResult.klasifikasi;
        }
      },
    },
    "todo.bb": {
      handler(newValue) {
        if (newValue && this.todo.tb) {
          const bmiResult = this.calculateBMI(newValue, this.todo.tb);
          this.todo.bmi = bmiResult.bmi.toString();
          this.todo.klasifikasi = bmiResult.klasifikasi;
        }
      },
    },
    "newTodo.tb": {
      handler(newValue) {
        if (newValue && this.newTodo.bb) {
          const bmiResult = this.calculateBMI(this.newTodo.bb, newValue);
          this.newTodo.bmi = bmiResult.bmi.toString();
          this.newTodo.klasifikasi = bmiResult.klasifikasi;
        }
      },
    },
    "newTodo.bb": {
      handler(newValue) {
        if (newValue && this.newTodo.tb) {
          const bmiResult = this.calculateBMI(newValue, this.newTodo.tb);
          this.newTodo.bmi = bmiResult.bmi.toString();
          this.newTodo.klasifikasi = bmiResult.klasifikasi;
        }
      },
    },
  },

  methods: {
    onRowsPerPageChange() {
      var mythis = this;
      mythis.grid.updateConfig({
        pagination: {
          limit: parseInt(mythis.rowsPerPage),
          server: {
            url: (prev, page, limit) =>
              `${prev}${prev.includes("?") ? "&" : "?"}limit=${limit}&offset=${
                page * limit
              }`,
          },
        },
      });
      mythis.refreshTable();
    },

    calculateBMI(bb, tb) {
      // Remove any commas and spaces
      const bbDecimal = parseFloat(bb.toString().replace(/[, ]/g, ""));
      const tbDecimal = parseFloat(tb.toString().replace(/[, ]/g, "")) / 100; // Convert cm to meters

      if (!bbDecimal || !tbDecimal) {
        return { bmi: "", klasifikasi: "" }; // Return empty strings if invalid
      }

      const bmi = bbDecimal / (tbDecimal * tbDecimal);
      const bmiRounded = Number(bmi.toFixed(2));

      let klasifikasi;
      if (bmiRounded < 18.5) {
        klasifikasi = "Kurus";
      } else if (bmiRounded >= 18.5 && bmiRounded < 25) {
        klasifikasi = "Normal";
      } else if (bmiRounded >= 25 && bmiRounded < 30) {
        klasifikasi = "Gemuk";
      } else {
        klasifikasi = "Obesitas";
      }

      return { bmi: bmiRounded, klasifikasi };
    },
    showAddModal() {
      this.modalAdd = true;
      this.resetNewTodo();
    },

    closeAddModal() {
      this.modalAdd = false;
      this.resetNewTodo();
    },

    resetNewTodo() {
      this.newTodo = {
        nama_toko: "",
        channel: "",
        tb: "",
        bb: "",
        klasifikasi: "",
        pk: "",
        target_siba: "",
        target_soba: "",
        siba: "",
        soba: "",
        achv_siba: "",
        achv_soba: "",
      };
      this.selectedcabangs = null;
      this.selectedProfile = null;
      this.selectedMonth = null;
      this.selectedYear = null;
      Object.keys(this.errorField).forEach((key) => {
        this.errorField[key] = false;
      });
    },

    async loadCabang() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/cabangalldata"
        );
        if (response.data) {
          // Map data untuk memastikan struktur yang benar
          this.cabang_list = response.data.map((cabang) => ({
            id: cabang.id,
            nama_cabang: cabang.nama_cabang,
          }));
          console.log("Loaded cabangs:", this.cabang_list);
        }
      } catch (error) {
        console.error("Error loading cabang:", error);
        toast.error("Gagal memuat data cabang", { theme: "colored" });
      }
    },
    async onChangeCabang(value) {
      try {
        this.selectedProfile = null;
        this.profiles = [];

        if (value) {
          // Dapatkan data cabang yang dipilih
          const selectedCabang = this.cabang_list.find(
            (c) => c.nama_cabang === value
          );
          if (selectedCabang) {
            // Set cabang_id ke newTodo
            this.newTodo.cabang_id = value;
            // Ambil data profiles berdasarkan cabang
            await this.getProfileByCabang(value);
          }
        }
      } catch (error) {
        console.error("Error in onChangeCabang:", error);
        toast.error("Gagal mengubah cabang", { theme: "colored" });
      }
    },

    async getProfileByCabang(nama_cabang) {
      try {
        // Tambahkan parameter cabang_id untuk filter
        const response = await axios.get(
          `${this.$root.apiHost}api/profilesalldata`,
          {
            params: {
              cabang_id: nama_cabang, // Gunakan nama cabang sebagai filter
            },
          }
        );

        if (response.data) {
          // Filter data hanya untuk cabang yang dipilih
          this.profiles = response.data.filter(
            (profile) => profile.cabang_id === nama_cabang
          );
          console.log(
            "Filtered profiles for cabang:",
            nama_cabang,
            this.profiles
          );
        }
      } catch (error) {
        console.error("Error fetching profiles:", error);
        toast.error("Gagal mengambil data karyawan", { theme: "colored" });
      }
    },

    async loadProfiles() {
      if (!this.selected_cabang) return;
      try {
        const response = await axios.get(
          this.$root.apiHost + `api/profiles/by-cabang/${this.selected_cabang}`
        );
        this.profiles_list = response.data.data;
      } catch (error) {
        console.error(error);
        toast.error("Gagal memuat data profiles", { theme: "colored" });
      }
    },
    toggleModalCsv() {
      this.modalCsv = !this.modalCsv;
      if (!this.modalCsv) {
        this.resetForm();
      }
    },

    async saveTodoBulky() {
      if (!this.csv) {
        toast.error("Please select a CSV file to import", {
          theme: "colored",
        });
        return;
      }

      try {
        this.$root.flagButtonLoading = true;

        const response = await axios.post(
          this.$root.apiHost + "api/temptReportSobaPk/storeBulky",
          {
            csv: this.csv,
            userid: this.userid,
          }
        );

        await Swal.fire(
          "Created!",
          response.data.message || "Data has been created successfully",
          "success"
        );

        this.resetForm();
        location.reload();
      } catch (error) {
        this.$root.flagButtonLoading = false;
        if (error.response?.status === 422) {
          const errors = error.response.data;
          Object.values(errors).forEach((error) => {
            toast.error(error, { theme: "colored" });
          });
        } else {
          toast.error(error.response?.data?.message || "An error occurred", {
            theme: "colored",
          });
        }
      }
    },
    resetForm() {
      var mythis = this;
      Object.keys(mythis.errorField).forEach(function (key) {
        mythis.errorField[key] = false;
        mythis.todo[key] = "";
      });
      mythis.errorList = "";
      // this.selectedcabangs = null;
      // this.selectedProfile = null;
      // this.profiles = [];
      // this.selectedMonth = null;
      // this.selectedYear = null;

      this.csv = null;
      this.modalCsv = false;

      // mythis.csv = null;
    },

    refreshTable() {
      var mythis = this;
      mythis.status_table = false;
      $("#wrapper2").remove();
      var e = $('<div id="wrapper2"></div>');
      $("#box").append(e);
      this.getTable();
    },

    getTable() {
      var mythis = this;
      this.grid = new Grid();
      this.grid.updateConfig({
        pagination: {
          limit: parseInt(mythis.rowsPerPage),
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
          "Cabang",
          "Nama BA",
          {
            name: "Bulan",
            formatter: (cell) => {
              // Array nama bulan
              const months = [
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
              // Konversi nomor bulan ke nama bulan (kurangi 1 karena array dimulai dari 0)
              return months[parseInt(cell) - 1] || cell;
            },
          },
          "Tahun",
          "Nama Toko",
          "Channel",
          "TB",
          "BB",
          {
            name: "BMI",
            formatter: (cell) => (cell ? parseFloat(cell).toFixed(2) : "N/A"),
          },
          "Klasifikasi",
          //"Join Date",
          "PK",
          {
            name: "Target Siba",
            formatter: (cell) => {
              return new Intl.NumberFormat("id-ID").format(cell);
            },
          },
          {
            name: "Target Soba",
            formatter: (cell) => {
              return new Intl.NumberFormat("id-ID").format(cell);
            },
          },
          {
            name: "Siba",
            formatter: (cell) => {
              return new Intl.NumberFormat("id-ID").format(cell);
            },
          },
          {
            name: "Soba",
            formatter: (cell) => {
              return new Intl.NumberFormat("id-ID").format(cell);
            },
          },
          {
            name: "Achievement Siba",
            formatter: (cell) => `${cell}%`,
          },
          {
            name: "Achievement Soba",
            formatter: (cell) => `${cell}%`,
          },
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
                    `<button data-id="${row.cells[0].data}" class="btn btn-sm btn-warning text-white" id="editData" data-toggle="tooltip" title="Edit" ><i class="fa fa-pencil-square-o"></i></button>`
                  )
                : mythis.$root.accessRoles[mythis.access_page].delete
                ? html(`
                      <button data-id="${row.cells[0].data}" class="btn btn-sm btn-danger text-white" id="deleteData" data-toggle="tooltip" title="Delete" ><i class="fa fa-trash-o"></i></button>`)
                : ``,
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
          url: this.$root.apiHost + "api/tempt_report_soba_pk",
          then: (data) => {
            // Update pagination info
            const offset = parseInt(data.offset || 0);
            const total = parseInt(data.count || 0);
            const limit = parseInt(this.rowsPerPage);

            this.totalRows = total;
            this.startRow = offset + 1;
            this.endRow = Math.min(offset + limit, total);

            if (total === 0) {
              this.startRow = 0;
              this.endRow = 0;
            }

            if (!data.results || !Array.isArray(data.results)) {
      console.error("Invalid data format from server", data);
      return [];
    }

            return data.results.map((card, index) => {
            const rowNumber = isNaN(offset + index + 1) ? index + 1 : offset + index + 1;
            return[
              card.id,
              rowNumber,
              card.cabang_id,
              card.profiles_id,
              card.mop,
              card.yop,
              card.nama_toko,
              card.channel,
              card.tb,
              card.bb,
              card.bmi,
              card.klasifikasi,
              // card.join_date,
              card.pk,
              card.target_siba,
              card.target_soba,
              card.siba,
              card.soba,
              card.achv_siba,
              card.achv_soba,
            ]});
          },
          total: (data) => data.count,
        },
      });

      this.grid.render(document.getElementById("wrapper2"));
      this.jqueryDelEdit();
      this.status_table = true;
    },

    show_modal(isOpen = false, isReadonly = false) {
      this.modal = isOpen;
      this.isReadonly = isReadonly;
      if (!isOpen) {
        this.resetForm();
      }
    },

    async jqueryDelEdit() {
      const mythis = this;

      // Hapus event listener sebelumnya
      $(document).off("click", "#editData");
      $(document).off("click", "#deleteData");

      // Tambahkan event handler baru
      $(document).on("click", "#editData", async function () {
        let id = $(this).data("id");
        mythis.show_modal(true, true);
        await mythis.getData(id);
      });

      $(document).on("click", "#deleteData", function () {
        let id = $(this).data("id");
        mythis.deleteTodo(id);
      });
    },

    async saveTodo() {
      try {
        this.$root.flagButtonLoading = true;

        // Frontend validation
        if (!this.selectedcabangs)
          throw new Error("Pilih Cabang terlebih dahulu");
        if (!this.selectedProfile)
          throw new Error("Pilih Karyawan terlebih dahulu");
        if (!this.selectedMonth) throw new Error("Pilih Bulan terlebih dahulu");
        if (!this.selectedYear) throw new Error("Pilih Tahun terlebih dahulu");
        if (!this.newTodo.nama_toko) throw new Error("Nama Toko harus diisi");

        // Calculate BMI if needed
        let bmi = null;
        let klasifikasi = null;
        if (this.newTodo.bb && this.newTodo.tb) {
          const bmiResult = this.calculateBMI(this.newTodo.bb, this.newTodo.tb);
          bmi = bmiResult.bmi.toString();
          klasifikasi = bmiResult.klasifikasi;
        }

        // Convert numeric values to strings
        const payload = {
          cabang_id: this.selectedcabangs,
          profiles_id: this.selectedProfile,
          mop: this.selectedMonth,
          yop: this.selectedYear,
          nama_toko: this.newTodo.nama_toko?.trim(),
          channel: this.newTodo.channel?.trim(),
          tb: this.newTodo.tb?.toString(),
          bb: this.newTodo.bb?.toString(),
          bmi: bmi,
          klasifikasi: klasifikasi,
          pk: this.newTodo.pk?.toString(),
          target_siba: this.newTodo.target_siba?.toString(),
          target_soba: this.newTodo.target_soba?.toString(),
          siba: this.newTodo.siba?.toString(),
          soba: this.newTodo.soba?.toString(),
          achv_siba: this.newTodo.achv_siba?.toString(),
          achv_soba: this.newTodo.achv_soba?.toString(),
          userid: this.userid,
        };

        // Send request
        const response = await axios.post(
          `${this.$root.apiHost}api/tempt_report_soba_pk`,
          payload
        );

        // Handle success
        if (response.data.status) {
          await Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: response.data.message,
          });
          this.closeAddModal();
          this.refreshTable();
        }
      } catch (error) {
        console.error("Full error:", error);

        if (error.response?.data?.errors) {
          Object.values(error.response.data.errors).forEach((messages) => {
            messages.forEach((message) => {
              toast.error(message, { theme: "colored" });
            });
          });
        } else if (error.message) {
          toast.error(error.message, { theme: "colored" });
        } else {
          toast.error("Terjadi kesalahan saat menyimpan data", {
            theme: "colored",
          });
        }
      } finally {
        this.$root.flagButtonLoading = false;
      }
    },

    editTodo() {
      var mythis = this;
      mythis.$root.flagButtonLoading = true;
      const config = {};

      const bmiResult = this.calculateBMI(mythis.todo.bb, mythis.todo.tb);

      axios
        .put(
          mythis.$root.apiHost + "api/tempt_report_soba_pk/" + mythis.todo.id,
          {
            cabang_id: mythis.todo.cabang_id,
            profiles_id: mythis.todo.profiles_id,
            mop: mythis.selectedMonth,
            yop: mythis.selectedYear,
            nama_toko: mythis.todo.nama_toko,
            channel: mythis.todo.channel,
            tb: mythis.todo.tb,
            bb: mythis.todo.bb,
            bmi: bmiResult.bmi.toString(),
            klasifikasi: bmiResult.klasifikasi,
            join_date: mythis.todo.join_date,
            pk: mythis.todo.pk,
            target_siba: mythis.todo.target_siba,
            target_soba: mythis.todo.target_soba,
            siba: mythis.todo.siba,
            soba: mythis.todo.soba,
            achv_siba: mythis.todo.achv_siba,
            achv_soba: mythis.todo.achv_soba,
            userid: mythis.userid,
          },
          config
        )
        .then((res) => {
          Swal.fire("Updated!", res.data.message, "success");
          mythis.$root.flagButtonLoading = false;
          mythis.resetForm();
          mythis.show_modal();
          mythis.refreshTable();
        })
        .catch(function (error) {
          mythis.$root.flagButtonLoading = false;
          if (error.response) {
            if (error.response.status == 422) {
              mythis.errorList = error.response.data;
              if (Object.keys(mythis.errorList).length > 0) {
                Object.keys(mythis.errorField).forEach(function (key) {
                  mythis.errorField[key] = false;
                });
                Object.keys(mythis.errorList).forEach(function (key) {
                  toast.error(mythis.errorList[key], { theme: "colored" });
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
          const config = {
            data: {
              userid: mythis.userid,
            },
          };

          axios
            .delete(
              mythis.$root.apiHost + `api/tempt_report_soba_pk/${id}`,
              config
            )
            .then((res) => {
              Swal.fire("Deleted!", "Data has been deleted", "success");
              mythis.$root.stopLoading();
              mythis.refreshTable();
              mythis.resetForm();
            });
        }
      });
    },

    async getData(id) {
      var mythis = this;
      mythis.flagButtonAdd = false;
      mythis.$root.presentLoading();
      mythis.todo = {};

      try {
        const response = await axios.get(
          mythis.$root.apiHost + `api/tempt_report_soba_pk/${id}`
        );

        const data = response.data.data;
        if (data) {
          // Set form data
          mythis.todo = {
            id: id,
            cabang_id: data.cabang_id,
            kode_lokasi: data.kode_lokasi || "",
            profiles_id: data.profiles_id,
            mop: data.mop,
            yop: data.yop,
            nama_toko: data.nama_toko,
            channel: data.channel,
            tb: data.tb,
            bb: data.bb,
            bmi: data.bmi,
            klasifikasi: data.klasifikasi,
            pk: data.pk,
            target_siba: data.target_siba,
            target_soba: data.target_soba,
            siba: data.siba,
            soba: data.soba,
            achv_siba: data.achv_siba,
            achv_soba: data.achv_soba,
          };

          // Set selected values for v-select
          mythis.selectedcabangs = data.cabang_id;
          mythis.selectedProfile = data.profiles_id;
          mythis.selectedMonth = data.mop;
          mythis.selectedYear = data.yop;

          // Calculate initial BMI if needed
          if (mythis.todo.tb && mythis.todo.bb && !mythis.todo.bmi) {
            const bmiResult = this.calculateBMI(mythis.todo.bb, mythis.todo.tb);
            mythis.todo.bmi = bmiResult.bmi.toString();
            mythis.todo.klasifikasi = bmiResult.klasifikasi;
          }

          // Tampilkan modal setelah data di-load
          mythis.modal = true;
        } else {
          console.error("No data found");
          toast.error("Data tidak ditemukan", { theme: "colored" });
        }

        mythis.$root.stopLoading();
      } catch (error) {
        console.error("Error fetching data:", error);
        mythis.$root.stopLoading();
        toast.error("Gagal mengambil data", { theme: "colored" });
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
  width: 90%;
  margin: 30px auto;
  max-width: 1800px;
}

.modal-body {
  overflow-x: auto; /* Tambahkan scroll horizontal jika konten terlalu lebar */
  padding: 15px;
}

/* Opsional: Sesuaikan tinggi modal jika diperlukan */
.modal-content {
  max-height: 90vh; /* 90% dari tinggi viewport */
  overflow-y: auto; /* Scroll vertikal jika konten terlalu panjang */
}

/* Pastikan header modal tetap di posisinya */
.modal-header {
  position: sticky;
  top: 0;
  background-color: white;
  z-index: 1;
}

/* Pastikan footer modal tetap di posisinya */
.modal-footer {
  position: sticky;
  bottom: 0;
  background-color: white;
  z-index: 1;
}

.form-control-sm {
  height: calc(1.5em + 0.5rem + 2px);
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
  line-height: 1.5;
  border-radius: 0.2rem;
}

.mt-3 {
  margin-top: 1rem !important;
}

.d-flex {
  display: flex !important;
}

.justify-content-between {
  justify-content: space-between !important;
}

.align-items-center {
  align-items: center !important;
}
</style>
