<template>
  <!-- Modal Keluar Karyawan -->
  <div
    :class="showTakeOutModal ? 'modal fade in' : 'modal fade'"
    id="takeOutModal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    data-keyboard="false"
    data-backdrop="true"
    :style="showTakeOutModal ? 'display: block;' : 'display: none;'"
  >
    <div class="modal-dialog2 modal-dialog-centered modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary" style="padding: 10px 15px">
          <h5 class="modal-title" style="margin: 0">
            <i class="fa fa-sign-out mr-2"></i>
            Form Keluar Karyawan
          </h5>
          <button
            type="button"
            class="close"
            @click="showTakeOutModal = false"
            aria-label="Close"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" style="padding: 15px">
          <div class="form-group">
            <label>Tanggal Keluar</label>
            <input
              type="date"
              v-model="takeOutData.tgl_take_out"
              class="form-control input-lg"
              required
            />
          </div>

          <div class="form-group">
            <label>Keterangan</label>
            <select
              v-model="takeOutData.ket"
              class="form-control input-lg"
              required
            >
              <option value="" disabled>Pilih Keterangan</option>
              <option
                v-for="option in takeOutOptions"
                :key="option.value"
                :value="option.value"
              >
                {{ option.label }}
              </option>
            </select>
          </div>

          <!-- Unit Tujuan dengan ukuran yang disesuaikan
          <div class="form-group" v-if="takeOutData.ket === 'PINDAH UNIT'">
            <label>Unit Tujuan</label>
            <select
              v-model="takeOutData.unit"
              class="form-control input-lg"
              required
            >
              <option value="" disabled>Pilih Unit</option>
              <option
                v-for="option in unitOptions"
                :key="option.value"
                :value="option.value"
              >
                {{ option.label }}
              </option>
            </select>
          </div>
        </div> -->

          <div class="modal-footer" style="padding: 10px 15px">
            <button
              type="button"
              class="btn btn-sm btn-secondary"
              @click="closeTakeOutModal"
            >
              Cancel
            </button>
            <button
              @click="saveTakeOut"
              type="button"
              class="btn btn-sm btn-primary"
              :disabled="!isFormValid"
            >
              <i
                v-if="$root.flagButtonLoading"
                class="fa fa-spinner fa-spin mr-2"
              ></i>
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL ALERT REMIDER -->
  <!-- Alert Kontrak Berakhir - tambahkan setelah modal dan sebelum content utama -->
  <!-- Modal Alert Kontrak -->
  <div
    :class="showAlertModal ? 'modal fade in' : 'modal fade'"
    id="alertKontrakModal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    data-keyboard="false"
    data-backdrop="static"
    :style="showAlertModal ? 'display: block;' : 'display: none;'"
  >
    <div class="modal-dialog2 modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h4 class="modal-title">
            <i class="fa fa-bell mr-2"></i>
            Peringatan Kontrak Akan Berakhir
          </h4>
          <button
            type="button"
            class="close text-white"
            @click="showAlertModal = false"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body p-0">
          <div v-if="kontrakBerakhir && kontrakBerakhir.length > 0">
            <div
              v-for="kontrak in kontrakBerakhir"
              :key="kontrak.id"
              class="contract-card p-4 mb-3 border-bottom"
              :class="{
                'bg-danger-light': kontrak.result === 'Tidak Diperpanjang',
                'bg-warning-light': kontrak.result === 'Dipertimbangkan',
                'bg-success-light': kontrak.result === 'Diperpanjang',
                'bg-secondary-light':
                  !kontrak.result || kontrak.result === 'Belum ada status',
              }"
            >
              <!-- Header dengan badge jabatan -->
              <div
                class="d-flex justify-content-between align-items-center mb-3"
              >
                <span class="badge badge-primary px-3 py-2">
                  {{ kontrak.jabatan_id }}
                </span>
                <span class="badge" :class="kontrak.resultClass">
                  {{ kontrak.result || "Belum ada status" }}
                </span>
              </div>

              <!-- Informasi kontrak -->
              <div class="contract-info mb-3">
                <h5 class="font-weight-bold mb-3">
                  {{ kontrak.no_kontrak }}
                </h5>

                <div class="info-grid">
                  <div class="info-item">
                    <i class="fa fa-user text-primary mr-2"></i>
                    <strong>{{ kontrak.profiles_id }}</strong>
                  </div>

                  <div class="info-item">
                    <i class="fa fa-building text-primary mr-2"></i>
                    {{ kontrak.cabang_id }}
                  </div>

                  <div class="info-item">
                    <i class="fa fa-file-text text-primary mr-2"></i>
                    {{ kontrak.tipe }}
                  </div>

                  <div class="info-item">
                    <i class="fa fa-calendar text-danger mr-2"></i>
                    <strong
                      >Berakhir: {{ formatTanggal(kontrak.tgl_keluar) }}</strong
                    >
                  </div>
                </div>
              </div>

              <!-- Tombol Add Data Kontrak -->
              <div
                v-if="
                  ['Dipertimbangkan', 'Diperpanjang'].includes(kontrak.result)
                "
                class="text-right"
              >
                <button
                  class="btn btn-success"
                  @click="prepareNewContract(kontrak)"
                >
                  <i class="fa fa-plus-circle mr-2"></i>
                  Add Data Kontrak
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeModal">
            <i class="fa fa-times mr-2"></i>
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL ALERT REMIDER -->
  <!-- MODAL TANGGAL DOWNLOAD PDF -->
  <!-- Modal Tanggal Cetak -->
  <div
    :class="showTanggalModal ? 'modal fade in' : 'modal fade'"
    id="tanggalModal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    data-keyboard="false"
    data-backdrop="true"
    :style="showTanggalModal ? 'display: block;' : 'display: none;'"
  >
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px">
      <div class="modal-content">
        <!-- Header -->
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">
            <i class="fa fa-file-pdf-o mr-2"></i>
            Export PDF Document
          </h5>
          <button
            type="button"
            class="close text-white"
            @click="closeTanggalModal"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body p-4">
          <div class="form-group mb-0">
            <label class="font-weight-bold mb-2">
              <i class="fa fa-calendar mr-2 text-danger"></i>
              Tanggal Cetak Document
            </label>
            <input
              type="date"
              v-model="tanggalCetak"
              :max="getCurrentDate()"
              class="form-control"
              :class="{ 'is-invalid': !tanggalCetak  }"
              required
            />
            <div class="invalid-feedback">Silakan pilih tanggal cetak</div>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            @click="closeTanggalModal"
          >
            <i class="fa fa-times mr-2"></i>
            Cancel
          </button>

          <button
            @click="handleExport"
            type="button"
            class="btn btn-danger"
            :disabled="!tanggalCetak"
          >
            <i
              v-if="$root.flagButtonLoading"
              class="fa fa-spinner fa-spin mr-2"
            ></i>
            <i v-else class="fa fa-file-pdf-o mr-2"></i>
            Export PDF
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL TANGGAL DOWNLOAD PDF -->

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
                  <label for="example-nf-email">Cabang</label>
                  <v-select
                    id="cabang_id"
                    variant="outlined"
                    label="nama_cabang"
                    :options="cabangs"
                    placeholder="Pilih Cabang"
                    v-model="selectedcabangs"
                    :reduce="(option) => option.nama_cabang"
                    @update:modelValue="onChangeCabang"
                  >
                  </v-select>
                </div>
              </div>
              <!-- :disabled="!flagButtonAdd" -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-nf-email">Nama Karyawan</label>
                  <v-select
                    id="profile_id"
                    variant="outlined"
                    label="profile_name"
                    :options="filteredProfiles"
                    placeholder="Pilih Karyawan"
                    v-model="selectedProfile"
                    :reduce="
                      (option) => ({
                        profile_name: option.profile_name,
                        id: option.id,
                      })
                    "
                    @update:modelValue="onChangeProfile"
                    :disabled="!flagButtonAdd || !todo.cabang_id"
                  >
                    <template #option="{ profile_name }">
                      {{ profile_name }}
                    </template>
                  </v-select>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-nf-email">No Kontrak</label>
                  <CmpInputText
                    id="inputA"
                    type="text"
                    placeholder="Auto Generate"
                    v-model="todo.no_kontrak"
                    :class="
                      errorField.no_kontrak
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                    @input="
                      (val) => (todo.no_kontrak = todo.no_kontrak.toUpperCase())
                    "
                    readonly
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-nf-email">Tipe</label>
                  <select
                    id="tipe"
                    placeholder="Tipe"
                    v-model="todo.tipe"
                    :class="
                      errorField.tipe
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                    @change="generateNoKontrak"
                    :disabled="!flagButtonAdd || !todo.profiles_id"
                  >
                    <option value="" disabled selected>
                      Pilih Tipe Kontrak
                    </option>
                    <option value="MAGANG">MAGANG</option>
                    <option value="PKWT">PKWT</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="tgl_masuk">Kontrak Awal</label>
                  <input
                    type="date"
                    v-model="todo.tgl_masuk"
                    :class="
                      errorField.tgl_masuk
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="masa_kerja">Masa Kerja</label>
                  <select
                    v-model="selectedTimeframe"
                    class="form-control input-lg"
                    @change="setTanggalKeluar"
                    :key="`timeframe-${todo.tgl_masuk}`"
                  >
                    <option disabled value="">Pilih Masa Kerja</option>
                    <optgroup label="Bulan">
                      <option
                        v-for="month in umonths"
                        :key="month"
                        :value="{ type: 'month', value: month }"
                      >
                        {{ month }} Bulan
                      </option>
                    </optgroup>
                    <optgroup label="Tahun">
                      <option
                        v-for="year in years"
                        :key="year"
                        :value="{ type: 'year', value: year }"
                      >
                        {{ year }} Tahun
                      </option>
                    </optgroup>
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="tgl_keluar">Kontrak Berakhir</label>
                      <input
                        type="date"
                        v-model="todo.tgl_keluar"
                        :class="
                          errorField.tgl_keluar
                            ? 'form-control input-lg input-error'
                            : 'form-control input-lg'
                        "
                        readonly
                      />
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="example-nf-email">Keterangan</label>
                      <textarea
                        id="inputA"
                        type="text"
                        placeholder="Keterangan"
                        v-model="todo.ket"
                        :class="
                          errorField.ket
                            ? 'form-control input-lg input-error'
                            : 'form-control input-lg'
                        "
                        @input="(val) => (todo.ket = todo.ket)"
                        rows="4"
                      />
                    </div>
                  </div>
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
                    todo.cabang_id == null ||
                    todo.cabang_id == '' ||
                    todo.no_kontrak == null ||
                    todo.no_kontrak == '' ||
                    todo.tipe == null ||
                    todo.tipe == '' ||
                    todo.tgl_masuk == null ||
                    todo.tgl_masuk == '' ||
                    todo.tgl_keluar == null ||
                    todo.tgl_keluar == '' ||
                    todo.ket == null ||
                    todo.ket == ''
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
                    todo.profiles_id == '' ||
                    todo.cabang_id == null ||
                    todo.cabang_id == '' ||
                    todo.no_kontrak == null ||
                    todo.no_kontrak == '' ||
                    todo.tipe == null ||
                    todo.tipe == '' ||
                    todo.tgl_masuk == null ||
                    todo.tgl_masuk == '' ||
                    todo.tgl_keluar == null ||
                    todo.tgl_keluar == '' ||
                    todo.ket == null ||
                    todo.ket == ''
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

  <div
    :class="modalpdf ? 'modal fade in' : 'modal fade'"
    id="exampleModalPdf"
    tabindex="-1"
    role="dialog"
    aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true"
    data-keyboard="false"
    data-backdrop="true"
    :style="modalpdf ? 'display: block;' : 'display: none;'"
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
            @click="show_modalpdf()"
          >
            <span aria-hidden="true">X</span>
          </button>
        </div>
        <div class="modal-body">
          <pre>{{ todopdf }}</pre>
          <!-- Wizards Row -->
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-nf-email">Nama Karyawan</label>
                  <v-select
                    id="profile_id"
                    :options="profilespdf"
                    variant="outlined"
                    label="profiles_id"
                    placeholder="Pilih Karyawan"
                    v-model="selectedProfilePdf"
                    @update:modelValue="onChangeProfilePdf()"
                  >
                  </v-select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="example-nf-email">No Kontrak</label>
                  <v-select
                    id="no_kontrak"
                    :options="noKontrakList"
                    variant="outlined"
                    label="no_kontrak"
                    placeholder="No Kontrak"
                    v-model="selectedNoKontrak"
                    @update:modelValue="onChangeKontrakPdf()"
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
                  @click="exportPdf()"
                  type="button"
                  class="btn btn-sm btn-primary pull-left"
                  :disabled="
                    $root.flagButtonLoading ||
                    todopdf.profiles_id == null ||
                    todopdf.profiles_id == '' ||
                    todopdf.no_kontrak == null ||
                    todopdf.no_kontrak == ''
                  "
                >
                  <i
                    v-if="$root.flagButtonLoading"
                    class="fa fa-spinner fa-spin text-default"
                  ></i>
                  DOWNLOAD SURAT KONTRAK
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!---------------------------- Modal -->

  <!-- Modal CSV -->
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
        <div
          class="modal-header bg-primary text-white d-flex justify-content-between align-items-center"
        >
          <h5 class="modal-title mb-0">
            <i class="fa fa-upload mr-2"></i>
            IMPORT CSV {{ $root.judulHalaman }}
          </h5>
          <button
            id="closeModal"
            type="button"
            class="close text-white"
            data-dismiss="modal"
            aria-label="Close"
            :disabled="$root.flagButtonLoading"
            @click="toggleModalCsv()"
            style="opacity: 1; font-size: 24px"
          >
            <span aria-hidden="true">&times;</span>
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

        <div class="d-flex align-items-start">
    <div >
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
        style="margin-left: 2px;"
          class="btn btn-sm btn-success pull-left"
          @click="download_excel_xyz()"
        >
          <i class="class fas fa-file-excel"></i>
          Export Excel
        </button>
      </download-excel>

      <button
        class="btn btn-sm btn-warning"
        @click="checkKontrakBerakhir"
        style="margin-left: 10px"
      >
        Alert Kontrak
      </button>

      <div class="ml-2 d-inline-block" style="margin-top: 10px">
        <v-select
          v-model="selectedFilterCabang"
          :options="cabangreal"
          label="nama_cabang"
          :placeholder="modal ? '' : 'Filter by Cabang'"
          @update:modelValue="filterByCabang"
          clearable
        >
        </v-select>
      </div>
    </div>

    <div class="pull-right" style="margin-left: auto">
      <button
        @click="toggleModalCsv()"
        class="btn btn-sm btn-info"
        style="margin-right: 8px"
      >
        <i class="fa fa-upload"></i> Import CSV
      </button>

      <button
        v-if="status_table && $root.accessRoles[access_page].create"
        class="btn btn-sm btn-primary"
        @click="show_modal()"
      >
        <i class="fa fa-plus"></i> ADD DATA
      </button>
    </div>
  </div>

        <!------------------------>
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

import logoSrc from "@/assets/img/logo-sgs.png";

import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";
import { readonly } from "vue";

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
        temp_profile_id: false,
        profiles_id: false,
        cabang_id: false,
        no_kontrak: false,
        tipe: false,
        tgl_masuk: false,
        tgl_keluar: false,
        tahun: false,
        bulan: false,
        hari: false,
        tgl_take_out: false,
        ket: false,
      },

      modalCsv: false,
      csv: null,
      dataImportCsv: {
        profiles_id: {
          label: "profiles_id",
          required: true,
        },
        cabang_id: {
          label: "cabang_id",
          required: true,
        },
        temp_profile_id: {
          label: "temp_profile_id",
          required: true,
        },
        no_kontrak: {
          label: "no_kontrak",
          required: true,
        },
        tipe: {
          label: "tipe",
          required: true,
        },
        tgl_masuk: {
          label: "tgl_masuk",
          required: true,
        },
        tgl_keluar: {
          label: "tgl_keluar",
          required: true,
        },
        tahun: {
          label: "tahun",
          required: true,
          type: "number",
        },
        bulan: {
          label: "bulan",
          required: true,
          type: "number",
        },
        hari: {
          label: "hari",
          required: true,
          type: "number",
        },
        ket: {
          label: "ket",
          required: false,
        },
      },

      userid: 0,
      status_table: false,

      modal: false,
      modalpdf: false,

      rowsPerPage: 10,
      startRow: 0,
      endRow: 0,
      totalRows: 0,

      selectedFilterCabang: null,
    cabangreal: [],

      todo: {
        profiles_id: "",
        temp_profile_id: "",
        cabang_id: "",
        no_kontrak: "",
        tipe: "",
        tgl_masuk: "",
        tgl_keluar: "",
        tahun: "",
        bulan: "",
        hari: "",
        tgl_take_out: null,
        ket: "",
      },

      todopdf: {
        profiles_id: "",
        no_kontrak: "",
      },

      flagButtonAdd: true,

      noKontrakList: [],
      profilespdf: [],
      selectedNoKontrak: null,
      selectedProfilePdf: null,

      temp_profile_id: null,
      selectedProfile: null,
      profiles: [],

      selectedcabangs: null,
      cabangs: [],

      umonths: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
      years: [1, 2, 3, 4, 5],
      selectedTimeframe: "",

      data_x_table: [],
      data_x_excel: [],
      nama_excelnya: "",
      nama_sheetnya: "Data Kontrak",
      json_data: [],
      json_meta: [[{ key: "charset", value: "UTF-8" }]],
      json_fields: {
        profiles_id: "profiles_id", //sesuaikan database yang diperlukan
        cabang_id: "cabang_id",
        no_kontrak: "no_kontrak",
        tipe: "tipe",
        tgl_masuk: "tgl_masuk",
        tgl_keluar: "tgl_keluar",
        tahun: "tahun",
        bulan: "bulan",
        hari: "hari",
        tgl_take_out: "tgl_take_out",
        tgl_make_kontrak: "tgl_make_kontrak",
        ket: "ket",
        ket_take_out: "ket_take_out",
      },

      showTanggalModal: false,
      tanggalCetak: "",
      selectedId: null,
      selectedType: null,

      showAlertModal: false,
      kontrakBerakhir: [],
      loading: false,

      showTakeOutModal: false,
      takeOutData: {
        id: null,
        profiles_id: "",
        tgl_take_out: "",
        ket: "",
        ket_take_out: "",
        //unit: "",
      },
      takeOutOptions: [
        { value: "RESIGN", label: "Resign" },
        { value: "HABIS KONTRAK", label: "Habis Kontrak" },
        { value: "PINDAH UNIT", label: "Pindah Unit" },
        { value: "PENSIUN", label: "Pensiun" },
      ],

      // unitOptions: [
      //   // Tambahkan opsi unit
      //   { value: "MB", label: "MB" },
      //   { value: "TPS", label: "TPS" },
      //   { value: "KBP", label: "KBP" },
      // ],

      days: ["MINGGU", "SENIN", "SELASA", "RABU", "KAMIS", "JUMAT", "SABTU"],
      months: [
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
      ],

      logoSrc: logoSrc,
      activeSignature: null,
    };
  },

  async mounted() {
    // await this.$root.refreshToken(localStorage.getItem("token"));
    this.getTable();
    this.userid = this.$root.get_id_user(localStorage.getItem("unique"));
    await this.getProfile();
    await this.getCabang();
    await this.getCabangReal();
    await this.getProfilePdf();
    try {
      await this.$nextTick();
      // Satu handler global untuk export
      window.handleExportClick = (id) => {
        console.log("Export clicked:", { id });
        this.selectedId = id;
        this.showTanggalModal = true; // Menggunakan variable Vue untuk kontrol modal
      };
    } catch (error) {
      console.error("Error in mounted:", error);
    }
    // Tambahkan ini di akhir mounted
    await this.checkKontrakBerakhir();
    // Set interval untuk cek setiap 24 jam
    setInterval(this.checkKontrakBerakhir, 24 * 60 * 60 * 1000);
    this.getActiveSignature();
  },

  computed: {
    isFormValid() {
      if (!this.takeOutData.tgl_take_out || !this.takeOutData.ket) {
        return false;
      }

      // // Jika PINDAH UNIT dipilih, unit harus diisi
      // if (this.takeOutData.ket === "PINDAH UNIT" && !this.takeOutData.unit) {
      //   return false;
      // }

      return true;
    },
    filteredProfiles() {
      if (!this.todo.cabang_id || !this.profiles) return [];

      const filtered = this.profiles
        .filter((profile) => profile.cabang_id === this.todo.cabang_id)
        .map((profile) => ({
          id: profile.id,
          profile_name: profile.profile_name,
          cabang_id: profile.cabang_id,
        }));

      console.log("Filtered profiles:", filtered);
      return filtered;
    },
  },

  watch: {
    rowsPerPage: {
      handler(newValue) {
        this.rowsPerPage = parseInt(newValue);
      },
      immediate: true,
    },

    "todo.tgl_masuk": function (newVal) {
      if (newVal) {
        // Reset selectedTimeframe ke nilai default/kosong
        this.selectedTimeframe = "";
        // Reset tgl_keluar
        this.todo.tgl_keluar = "";
        // Reset perhitungan masa kerja
        this.todo.tahun = "";
        this.todo.bulan = "";
        this.todo.hari = "";
      }
    },
  },

  methods: {

    async getCabangReal() {
  try {
    const response = await axios.get(this.$root.apiHost + "api/cabangalldata");
    this.cabangreal = response.data.map((cabang) => ({
      nama_cabang: cabang.nama_cabang,
      kode_cabang: cabang.kode_cabang,
    }));
  } catch (error) {
    console.error("Error fetching cabang:", error);
    toast.error("Gagal mengambil data cabang", { theme: "colored" });
  }
},

    async filterByCabang(selected) {
  try {
    if (selected) {
      console.log("Selected cabang:", selected);

      // Update grid config with URL that includes the filter
      const baseUrl = `${this.$root.apiHost}api/hr_kontrak`;
      const url = `${baseUrl}?cabang_id=${encodeURIComponent(selected.nama_cabang)}`;

      // Update grid config with new URL
      const currentConfig = this.grid.config;
      this.grid.updateConfig({
        ...currentConfig,
        server: {
          ...currentConfig.server,
          url: url,
        },
      });

      // Refresh grid
      await this.grid.forceRender();
    } else {
      // Reset to original URL if no cabang is selected
      this.grid.updateConfig({
        ...this.grid.config,
        server: {
          ...this.grid.config.server,
          url: `${this.$root.apiHost}api/hr_kontrak`,
        },
      });
      await this.grid.forceRender();
    }
  } catch (error) {
    console.error("Error in filterByCabang:", error);
    toast.error("Gagal melakukan filter", { theme: "colored" });
  }
},

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

    hitungDurasiKerjaAktual(tglMasuk, tglTakeOut) {
      const masuk = new Date(tglMasuk);
      const keluar = new Date(tglTakeOut);

      let tahun = 0;
      let bulan = 0;
      let hari = 0;

      // Hitung selisih dalam milisecond
      let selisih = keluar - masuk;

      // Konversi ke hari
      const totalHari = Math.floor(selisih / (1000 * 60 * 60 * 24));

      // Hitung tahun
      tahun = Math.floor(totalHari / 365);
      selisih = totalHari % 365;

      // Hitung bulan (menggunakan 30 hari per bulan untuk simplicitas)
      bulan = Math.floor(selisih / 30);

      // Hitung sisa hari
      hari = selisih % 30;

      return { tahun, bulan, hari };
    },
    resetTakeOutForm() {
      this.takeOutData = {
        id: null,
        profiles_id: "",
        tgl_take_out: "",
        ket: "",
        ket_take_out: "",
        unit: "",
      };
    },

    closeTakeOutModal() {
      this.showTakeOutModal = false;
      this.resetTakeOutForm();
    },

    showTakeOutForm(id, profileId) {
      this.resetTakeOutForm(); // Reset form before showing
      this.takeOutData.id = id;
      this.takeOutData.profiles_id = profileId;
      this.showTakeOutModal = true;
    },

    async getActiveSignature() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/signature-master/active"
        );
        this.activeSignature = response.data;
      } catch (error) {
        console.error("Error fetching active signature:", error);
        // Use default values if no signature is found
        this.activeSignature = {
          name: "Fransiska Margi Tri Rahayu",
          position: "Jr. HRGA Manager",
        };
      }
    },
    toggleModalCsv() {
      this.modalCsv = !this.modalCsv;
      if (!this.modalCsv) {
        this.csv = null;
      }
    },

    async saveTodoBulky() {
      try {
        const result = await Swal.fire({
          title: "Import Data Kontrak",
          text: "Are you sure?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes",
          cancelButtonText: "Cancel",
        });

        if (result.isConfirmed) {
          const response = await axios.post(
            this.$root.apiHost + "api/hr_kontrak/storeBulky",
            {
              csv: this.csv,
              userid: this.userid,
              todo: this.todo,
            }
          );

          await Swal.fire(
            "Success!",
            response.data.message || "Data berhasil diimport",
            "success"
          );

          this.resetForm();
          this.csv = null;
          location.reload();
        }
      } catch (error) {
        this.$root.flagButtonLoading = false;

        if (error.response) {
          if (error.response.status === 422) {
            this.errorList = error.response.data;
            Object.keys(this.errorField).forEach((key) => {
              this.errorField[key] = false;
            });

            Object.keys(this.errorList).forEach((key) => {
              toast.error(this.errorList[key], { theme: "colored" });
              if (key in this.errorField) {
                this.errorField[key] = true;
              }
            });
          } else {
            toast.error(error.response.data.message, { theme: "colored" });
          }
        } else if (error.request) {
          console.log(error.request);
          toast.error("Network error occurred", { theme: "colored" });
        } else {
          console.log("Error", error.message);
          toast.error("An unexpected error occurred", { theme: "colored" });
        }
      }
    },
    async prepareNewContract(kontrak) {
      // Reset form
      this.resetForm();

      // Set data awal untuk kontrak baru
      this.todo = {
        profiles_id: kontrak.profiles_id,
        cabang_id: kontrak.cabang_id,
        tipe: kontrak.tipe,
        tgl_masuk: this.getNextDay(kontrak.tgl_keluar), // Tanggal mulai = tanggal berakhir + 1 hari
        temp_profile_id: "", // Akan diset setelah mendapatkan data profile
        tgl_keluar: "",
        tahun: "",
        bulan: "",
        hari: "",
        ket: `Perpanjangan kontrak dari ${kontrak.no_kontrak}`,
      };

      try {
        // Dapatkan data profile berdasarkan profiles_id
        const profileResponse = await axios.get(
          this.$root.apiHost + "api/profilesalldata"
        );
        const profile = profileResponse.data.find(
          (p) => p.profile_name === kontrak.profiles_id
        );

        if (profile) {
          this.todo.temp_profile_id = profile.id;
          // Set selected profile untuk v-select
          this.selectedProfile = {
            profile_name: profile.profile_name,
            id: profile.id,
          };
          // Set selected cabang untuk v-select
          this.selectedcabangs = kontrak.cabang_id;
        }

        // Generate nomor kontrak baru
        await this.generateNoKontrak();

        // Tutup modal alert
        this.showAlertModal = false;

        // Buka modal add data
        this.show_modal();
      } catch (error) {
        console.error("Error preparing new contract:", error);
        Swal.fire("Error", "Gagal mempersiapkan data kontrak baru", "error");
      }
    },

    getNextDay(date) {
      const nextDay = new Date(date);
      nextDay.setDate(nextDay.getDate() + 1);
      return nextDay.toISOString().split("T")[0];
    },

    async getSallaryByCabang(cabangId) {
      try {
        console.log("Mencari gaji untuk cabang:", cabangId);

        // Ambil semua data gaji
        const response = await axios.get(
          `${this.$root.apiHost}api/sallaryalldata`
        );

        console.log("Response data lengkap:", response.data);

        // Cari data yang sesuai dengan cabang_id
        const salaryData = response.data.find(
          (item) => item.cabang_id === cabangId
        );

        console.log("Data gaji yang ditemukan:", salaryData);

        if (salaryData && salaryData.sallary) {
          // Konversi string gaji ke number dan format
          const salary = parseFloat(salaryData.sallary);
          const formattedSalary = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          }).format(salary);

          console.log("Gaji yang diformat:", formattedSalary);
          return formattedSalary;
        }

        console.log("Data gaji tidak ditemukan untuk cabang:", cabangId);
        return "BELUM DIATUR";
      } catch (error) {
        console.error("Error mengambil data gaji:", error);
        return "BELUM DIATUR";
      }
    },
    showTakeOutForm(id, profileId) {
      this.takeOutData.id = id;
      this.takeOutData.profiles_id = profileId;
      this.showTakeOutModal = true;
    },

    // Method untuk menyimpan data keluar karyawan
    async saveTakeOut() {
      try {
        this.$root.presentLoading();

        // 1. Verifikasi ketersediaan data kontrak
        const kontrakResponse = await axios.get(
          this.$root.apiHost + `api/hr_kontrak/${this.takeOutData.id}`
        );
        const currentKontrak = kontrakResponse.data.data;

        const durasi = this.hitungDurasiKerjaAktual(
          currentKontrak.tgl_masuk,
          this.takeOutData.tgl_take_out
        );

        // 2. Periksa apakah kontrak sudah diproses sebelumnya
        if (currentKontrak.tgl_take_out) {
          throw new Error(
            "Kontrak ini sudah diproses untuk take out sebelumnya"
          );
        }

        // 3. Dapatkan dan verifikasi data profil
        const profilesResponse = await axios.get(
          this.$root.apiHost + "api/profilesalldata"
        );

        const profile = profilesResponse.data.find(
          (p) => p.profile_name === currentKontrak.profiles_id
        );

        if (!profile) {
          throw new Error(
            `Profil dengan nama ${currentKontrak.profiles_id} tidak ditemukan`
          );
        }

        // 4. Verifikasi status aktif profil
        if (profile.is_active !== "Aktif") {
          throw new Error("Profil karyawan sudah berstatus tidak aktif");
        }

        let finalKeterangan = this.takeOutData.ket;
        if (this.takeOutData.ket === "PINDAH UNIT") {
          finalKeterangan = "PINDAH UNIT";
        }

        // 5. Siapkan data untuk update kontrak
        const kontrakPayload = {
          ...currentKontrak,
          tgl_take_out: this.takeOutData.tgl_take_out,
          ket_take_out: finalKeterangan,
          tahun: durasi.tahun.toString(), // Update tahun
          bulan: durasi.bulan.toString(), // Update bulan
          hari: durasi.hari.toString(), // Update hari
          ket: currentKontrak.ket,
          userid: this.userid,
          _method: "PUT",
        };

        delete kontrakPayload.id;
        delete kontrakPayload.created_at;
        delete kontrakPayload.updated_at;

        // 6. Siapkan data untuk update profil
        const profilePayload = {
          ...profile,
          is_active: "Tidak Aktif",
          updated_by: this.userid,
          updated_at: new Date().toISOString(),
          _method: "PUT",
        };

        delete profilePayload.id;
        delete profilePayload.created_at;
        delete profilePayload.deleted_at;

        // 7. Lakukan update dengan penanganan konkurensi
        try {
          // Update kontrak terlebih dahulu
          await axios.put(
            this.$root.apiHost + `api/hr_kontrak/${this.takeOutData.id}`,
            kontrakPayload
          );

          // Jika berhasil, update profil
          await axios.put(
            this.$root.apiHost + `api/profiles/${profile.id}`,
            profilePayload
          );

          this.$root.stopLoading();
          await Swal.fire(
            "Berhasil",
            "Proses keluar karyawan telah berhasil disimpan",
            "success"
          );

          this.closeTakeOutModal();
          this.refreshTable();
        } catch (updateError) {
          if (updateError.response?.status === 409) {
            // Tangani konflik konkurensi
            await Swal.fire({
              title: "Perhatian",
              text: "Data telah diubah oleh pengguna lain. Silakan muat ulang data terbaru.",
              icon: "warning",
              showCancelButton: true,
              confirmButtonText: "Muat Ulang",
              cancelButtonText: "Batal",
            }).then((result) => {
              if (result.isConfirmed) {
                this.refreshTable();
              }
            });
          } else {
            throw updateError; // Teruskan error lainnya ke catch block utama
          }
        }
      } catch (error) {
        this.$root.stopLoading();

        let pesanError = "Gagal memproses keluar karyawan";

        if (error.response) {
          switch (error.response.status) {
            case 422:
              pesanError = Object.values(error.response.data).join("\n");
              break;
            case 409:
              pesanError =
                "Terjadi konflik saat memperbarui data. Silakan coba lagi.";
              break;
            case 500:
              pesanError = "Terjadi kesalahan pada server";
              break;
            default:
              pesanError = error.response.data?.message || pesanError;
          }
        } else if (error.message) {
          pesanError = error.message;
        }

        await Swal.fire("Gagal", pesanError, "error");
        console.error("Error proses keluar karyawan:", error);
      }
    },
    async checkKontrakBerakhir() {
      this.loading = true;
      try {
        // Ambil data kontrak yang akan berakhir
        const response = await axios.get(
          this.$root.apiHost + "api/check-expiring"
        );

        if (response.data && response.data.length > 0) {
          // Untuk setiap kontrak, cek apakah ini adalah kontrak terakhir
          const validContracts = [];

          for (const kontrak of response.data) {
            // Tambah pengecekan status aktif karyawan
            const profileResponse = await axios.get(
              this.$root.apiHost + "api/profilesalldata"
            );

            const profile = profileResponse.data.find(
              (p) => p.profile_name === kontrak.profiles_id
            );

            // Hanya lanjutkan jika karyawan masih aktif
            if (profile && profile.is_active === "Aktif") {
              const lastExitResponse = await axios.get(
                this.$root.apiHost +
                  `api/lastExitDate?profiles_id=${kontrak.profiles_id}`
              );

              if (lastExitResponse.data.tgl_keluar === kontrak.tgl_keluar) {
                try {
                  // Ambil data result langsung dari temp-pos-detail
                  const resultResponse = await axios.get(
                    this.$root.apiHost + "api/temp-pos-detail",
                    {
                      params: {
                        profiles_id: kontrak.profiles_id,
                      },
                    }
                  );

                  // Cek apakah ada data result untuk profile ini
                  const resultData = resultResponse.data.results.find(
                    (item) => item.profiles_id === kontrak.profiles_id
                  );

                  if (resultData) {
                    kontrak.result = resultData.result;
                    kontrak.resultClass = this.getResultClass(
                      resultData.result
                    );
                  } else {
                    kontrak.result = "Belum ada status";
                    kontrak.resultClass = "badge-secondary";
                  }
                } catch (error) {
                  console.error("Error fetching result:", error);
                  kontrak.result = "Belum ada status";
                  kontrak.resultClass = "badge-secondary";
                }

                validContracts.push(kontrak);
              }
            }
          }

          this.kontrakBerakhir = validContracts;

          if (this.kontrakBerakhir.length > 0) {
            this.showAlertModal = true;
            console.log("Kontrak yang akan berakhir:", this.kontrakBerakhir);
          } else {
            this.showAlertModal = false;
            Swal.fire({
              title: "Info",
              text: "Tidak ada kontrak aktif yang akan berakhir dalam 30 hari kedepan",
              icon: "info",
            });
          }
        } else {
          this.showAlertModal = false;
          Swal.fire({
            title: "Info",
            text: "Tidak ada kontrak yang akan berakhir dalam 30 hari kedepan",
            icon: "info",
          });
        }
      } catch (error) {
        console.error("Error:", error);
        Swal.fire({
          title: "Error",
          text: "Terjadi kesalahan saat mengecek kontrak",
          icon: "error",
        });
      } finally {
        this.loading = false;
      }
    },

    // Helper method untuk menentukan class badge
    getResultClass(result) {
      switch (result) {
        case "Diperpanjang":
          return "badge-success";
        case "Dipertimbangkan":
          return "badge-warning";
        case "Tidak Diperpanjang":
          return "badge-danger";
        default:
          return "badge-secondary";
      }
    },
    // Fungsi untuk menutup modal
    closeModal() {
      this.showAlertModal = false;
      this.modal = false;
      this.kontrakBerakhir = [];
    },

    formatTanggal(tanggal) {
      return new Date(tanggal).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
      });
    },

    getCurrentDate() {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, "0");
      const day = String(today.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },
    closeTanggalModal() {
      this.showTanggalModal = false;
      this.tanggalCetak = "";
      this.selectedId = null;
      this.selectedType = null;
    },

    async handleExport() {
      if (!this.tanggalCetak) {
        alert("Silahkan pilih tanggal cetak terlebih dahulu");
        return;
      }

      try {
        // Ambil data kontrak terlebih dahulu
        const kontrakReq = await axios({
          method: "get",
          url: this.$root.apiHost + `api/hr_kontrak/${this.selectedId}`,
        });
        const kontrakData = kontrakReq.data.data;

        // Update dengan semua field yang required
        await axios.put(
          this.$root.apiHost + `api/hr_kontrak/${this.selectedId}`,
          {
            profiles_id: kontrakData.profiles_id,
            cabang_id: kontrakData.cabang_id,
            no_kontrak: kontrakData.no_kontrak,
            tipe: kontrakData.tipe,
            tgl_masuk: kontrakData.tgl_masuk,
            tgl_keluar: kontrakData.tgl_keluar,
            tahun: kontrakData.tahun,
            bulan: kontrakData.bulan,
            hari: kontrakData.hari,
            ket: kontrakData.ket,
            tgl_make_kontrak: this.tanggalCetak, // Tambahkan tanggal pembuatan
            _method: "PUT",
          }
        );

        const tipe = kontrakData.tipe; // Ambil tipe dari data kontrak

        const selectedDate = new Date(this.tanggalCetak);
        const dayName = this.days[selectedDate.getDay()];
        const date = selectedDate.getDate();
        const month = this.months[selectedDate.getMonth()];
        const year = selectedDate.getFullYear();

        const dateInfo = { dayName, date, month, year };

        if (tipe === "MAGANG") {
          await this.exportPdf1(this.selectedId, dateInfo);
        } else if (tipe === "PKWT") {
          await this.exportPdf2(this.selectedId, dateInfo);
        } else {
          alert("Silahkan pilih tipe kontrak yang valid");
          return;
        }

        // Tutup modal
        this.closeTanggalModal();

        this.refreshTable();
      } catch (error) {
        console.error("Error exporting PDF:", error);
        alert("Terjadi kesalahan saat export PDF");
      }
    },

    convertToRoman(month) {
      const romanMonths = [
        "I",
        "II",
        "III",
        "IV",
        "V",
        "VI",
        "VII",
        "VIII",
        "IX",
        "X",
        "XI",
        "XII",
      ];
      return romanMonths[month - 1]; // Mengonversi bulan menjadi angka Romawi (1-based index)
    },

    async addHeaderFooter(doc) {
      // Tambahkan logo dengan margin
      const logoWidth = 150;
      const logoHeight = 50;
      const margin = 40;

      // Header section
      doc.addImage(
        "/src/assets/img/logo-sgs.png",
        "PNG",
        margin,
        20,
        logoWidth,
        logoHeight
      );

      // Tambahkan garis tebal abu-abu di bawah logo
      doc.setDrawColor(128, 128, 128);
      doc.setLineWidth(2);
      doc.line(margin, 80, doc.internal.pageSize.getWidth() - margin, 80);

      // Footer section dimulai lebih tinggi untuk memberikan ruang yang cukup
      const footerStartY = doc.internal.pageSize.getHeight() - 60;

      doc.setFontSize(10);
      doc.setTextColor(0, 0, 45);

      // Alamat utama
      const alamatUtama =
        "Komplek Buaran Persada No. 26 – 27 Jl. Jend. Pol. R. Soekamto Duren Sawit, Jakarta Timur";
      const telpFax = "Telp (021) 86612363 , 86612364 Fax. (021) 86612364";
      const repOffice = "Representative Office:";
      const alamatRep =
        "Gedung Holding Marthaa Tilaar Group ll- ll Telp (021) 46826130";

      // Posisi footer yang disesuaikan
      doc.text(
        alamatUtama,
        doc.internal.pageSize.getWidth() / 2,
        footerStartY,
        { align: "center" }
      );
      doc.text(
        telpFax,
        doc.internal.pageSize.getWidth() / 2,
        footerStartY + 10,
        { align: "center" }
      );

      doc.setFont("helvetica", "bold");
      doc.text(
        repOffice,
        doc.internal.pageSize.getWidth() / 2,
        footerStartY + 20,
        { align: "center" }
      );

      doc.setFont("helvetica", "normal");
      doc.text(
        alamatRep,
        doc.internal.pageSize.getWidth() / 2,
        footerStartY + 30,
        { align: "center" }
      );

      doc.setTextColor(0, 0, 0);

      return {
        headerHeight: 100, // Mulai konten setelah header (80 + 20 margin)
        footerHeight: 70, // Beri ruang untuk footer
        leftMargin: margin,
        rightMargin: margin,
        contentWidth: doc.internal.pageSize.getWidth() - 2 * margin,
      };
    },

    checkNewPage(doc, currentY, margins, estimatedHeight = 20) {
      const pageHeight = doc.internal.pageSize.getHeight();
      const availableSpace = pageHeight - margins.footerHeight - currentY;

      if (availableSpace < estimatedHeight) {
        doc.addPage();
        this.addHeaderFooter(doc); // Tambahkan header & footer di halaman baru
        return margins.headerHeight;
      }

      return currentY;
    },

    //async export PDF1 MAGANG/////////////////////////////////////////////////////////

    async exportPdf1(kontrakId, dateInfo) {
      const mythis = this;
      mythis.$root.presentLoading();
      await this.getActiveSignature();

      try {
        // Memanggil data kontrak
        const kontrakReq = await axios({
          method: "get",
          url: mythis.$root.apiHost + `api/hr_kontrak/${kontrakId}`,
        });
        const kontrakData = kontrakReq.data.data;
        console.log("Data Kontrak:", kontrakData);

        // Memanggil data profile berdasarkan ID profile
        const profileReq = await axios({
          method: "get",
          url: mythis.$root.apiHost + "api/profilesalldata",
        });
        const profileData = profileReq.data.find(
          (p) => p.profile_name === kontrakData.profiles_id
        );

        if (!profileData) {
          throw new Error(
            `Profile dengan nama ${kontrakData.profiles_id} tidak ditemukan`
          );
        }

        console.log("Data Profile:", profileData);

        const header = {
          ...kontrakData,
          ...profileData,
        };

        const doc = new jsPDF("p", "pt", "a4");
        const pageMargins = await this.addHeaderFooter(doc);
        let currentY = pageMargins.headerHeight;

        // Konstanta untuk posisi horizontal
        const labelX = 40;
        const colonX = 180;
        const valueX = 200;
        const indentX = 55;
        const maxWidth = doc.internal.pageSize.getWidth() - 2 * labelX;

        // Set judul dengan underline
        currentY += 20;
        doc.setFontSize(16);
        doc.setFont("helvetica", "bold");
        const title = "PERJANJIAN MAGANG";
        const titleWidth =
          doc.getStringUnitWidth(title) * doc.internal.getFontSize();
        const titleX = (doc.internal.pageSize.getWidth() - titleWidth) / 2;
        doc.text(title, doc.internal.pageSize.getWidth() / 2, currentY, {
          align: "center",
        });

        // Menambahkan garis bawah pada judul
        doc.line(titleX, currentY + 2, titleX + titleWidth, currentY + 2);

        // Reset font size to 11 for the rest of the document
        doc.setFontSize(11);

        // Nomor kontrak
        currentY += 20;
        doc.setFontSize(11);
        const noKontrak = `${header.no_kontrak || "No. ??/SGS-HRD/??/2024"}`;
        doc.setTextColor(0, 0, 0);
        doc.text(noKontrak, doc.internal.pageSize.getWidth() / 2, currentY, {
          align: "center",
        });

        // Reset font to normal
        doc.setFont("helvetica", "normal");

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

        // Fungsi untuk memformat tanggal
        function formatDate(dateString) {
          if (!dateString) return "INPUT OTOMATIS";
          try {
            const cleanDate = dateString.replace(/-/g, "/");
            const dateParts = cleanDate.split("/");
            if (dateParts.length !== 3) return dateString;
            const year = dateParts[0];
            const month = dateParts[1];
            const date = dateParts[2];
            if (month < 1 || month > 12) return dateString;
            const monthName = months[parseInt(month) - 1];
            return `${parseInt(date)} ${monthName} ${year}`;
          } catch (error) {
            return dateString;
          }
        }

        const addMultiLineText = (
          text,
          startX,
          startY,
          lineHeight,
          maxWidth
        ) => {
          doc.setFont("helvetica", "normal");
          // Split text into lines based on maxWidth
          const lines = doc.splitTextToSize(text, maxWidth);

          // Process each line for bold text
          lines.forEach((line, index) => {
            // Find instances of "PIHAK PERTAMA" and "PIHAK KEDUA"
            const words = line.split(/\b(PIHAK PERTAMA|PIHAK KEDUA)\b/);
            let currentX = startX;

            words.forEach((word) => {
              if (word === "PIHAK PERTAMA" || word === "PIHAK KEDUA") {
                doc.setFont("helvetica", "bold");
                doc.text(word, currentX, startY + index * lineHeight);
                const width =
                  (doc.getStringUnitWidth(word) * doc.internal.getFontSize()) /
                  doc.internal.scaleFactor;
                currentX += width;
                doc.setFont("helvetica", "normal");
              } else if (word) {
                doc.text(word, currentX, startY + index * lineHeight);
                const width =
                  (doc.getStringUnitWidth(word) * doc.internal.getFontSize()) /
                  doc.internal.scaleFactor;
                currentX += width;
              }
            });
          });

          return startY + lines.length * lineHeight;
        };

        // Informasi PIHAK PERTAMA
        currentY += 35;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 80);
        doc.text("1. Nama", labelX, currentY);
        doc.text(":", colonX, currentY);
        doc.setFont("helvetica", "bold");
        doc.text(this.activeSignature.name, valueX, currentY);
        doc.setFont("helvetica", "normal");

        currentY += 20;
        doc.text("Jabatan", indentX, currentY);
        doc.text(":", colonX, currentY);
        doc.setFont("helvetica", "bold");
        doc.text(
          `${this.activeSignature.position} PT. Sinergi Global Servis`, // Gunakan template literal
          valueX,
          currentY
        );
        doc.setFont("helvetica", "normal");

        // Paragraf PIHAK PERTAMA
        currentY += 25;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);
        const pihakPertamaText =
          "Dalam hal ini bertindak dalam jabatannya tersebut untuk dan atas nama PT. Sinergi Global Servis, perseroan yang beralamat di Kompleks Buaran Persada No. 26 - 27 Jl. Jend. Pol. R. Sukamto, Duren Sawit, Jakarta Timur, yang selanjutnya disebut PIHAK PERTAMA.";
        doc.text(pihakPertamaText, labelX, currentY, {
          maxWidth: maxWidth,
          align: "justify",
        });

        // Informasi PIHAK KEDUA
        currentY += 50;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 100);
        doc.text("2. Nama", labelX, currentY);
        doc.text(":", colonX, currentY);
        doc.text(header.profile_name || "INPUT OTOMATIS", valueX, currentY);

        currentY += 20;
        doc.text("Tempat, tanggal lahir", indentX, currentY);
        doc.text(":", colonX, currentY);
        const birthInfo = header.birthplace
          ? header.birthplace + ", " + formatDate(header.birthdate)
          : "INPUT OTOMATIS";
        doc.text(birthInfo, valueX, currentY);

        currentY += 20;
        doc.text("No Identitas", indentX, currentY);
        doc.text(":", colonX, currentY);
        doc.text(header.identity_number || "INPUT OTOMATIS", valueX, currentY);

        currentY += 20;
        doc.text("Alamat", indentX, currentY);
        doc.text(":", colonX, currentY);
        doc.text(header.profile_address || "INPUT OTOMATIS", valueX, currentY, {
          maxWidth: maxWidth - valueX + labelX,
        });

        // Paragraf PIHAK KEDUA
        currentY += 30;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
        const pihakKeduaText =
          "Dalam hal ini bertindak untuk dan atas nama diri sendiri, selanjutnya disebut sebagai PIHAK KEDUA.";
        doc.text(pihakKeduaText, labelX, currentY, { maxWidth: maxWidth });

        // Paragraf penutup
        currentY += 20;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
        doc.text(
          "Para pihak sepakat untuk mengadakan perjanjian magang dengan syarat dan ketentuan sebagai berikut:",
          labelX,
          currentY,
          { maxWidth: maxWidth, align: "justify" }
        );

        // Fungsi helper untuk menambah pasal
        const addPasal = (number, title, currentY) => {
          currentY += 15;
          currentY = this.checkNewPage(doc, currentY, pageMargins, 65);
          doc.setFont("helvetica", "bold");
          doc.setFontSize(11);
          doc.text(
            `PASAL ${number}`,
            doc.internal.pageSize.getWidth() / 2,
            currentY,
            {
              align: "center",
            }
          );

          currentY += 20;
          doc.text(title, doc.internal.pageSize.getWidth() / 2, currentY, {
            align: "center",
          });

          doc.setFont("helvetica", "normal");
          return currentY + 25;
        };

        currentY += 15;
        // PASAL 1
        currentY = addPasal(1, "PROGRAM MAGANG", currentY);

        // PASAL 1 content
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);
        const pasal1Text = `PIHAK PERTAMA menerima PIHAK KEDUA sebagai Tenaga Magang dan PIHAK KEDUA menyetujui dipekerjakan sebagai Tenaga Magang oleh PIHAK PERTAMA terhitung mulai tanggal ${formatDate(
          header.tgl_masuk
        )} sampai dengan ${formatDate(header.tgl_keluar)} sebagai ${
          header.jabatan_id
        } Cab. ${header.cabang_id} ditempatkan di PT. ${header.unit_id}.`;
        currentY = addMultiLineText(pasal1Text, labelX, currentY, 15, maxWidth);

        // PASAL 2
        currentY = addPasal(2, "RUANG LINGKUP", currentY);

        // PASAL 2 content
        const pasal2Texts = [
          "1) Program Magang adalah suatu program pelatihan yang diadakan oleh PIHAK PERTAMA untuk melatih dan membina para peserta agar memperoleh pengetahuan, pengalaman dan ketrampilan di bidang pekerjaan tertentu sesuai dengan standar yang dibuat PIHAK PERTAMA sehingga pada akhir program, PIHAK KEDUA dapat memiliki kompetensi yang diharapkan oleh PIHAK PERTAMA.",
          "2) Program Magang bukan merupakan perjanjian kerja antara PIHAK PERTAMA dan PIHAK KEDUA, dengan demikian PIHAK KEDUA setuju untuk melepaskan segala hak-hak kekaryawanan yang diatur dalam Undang-Undang Ketenagakerjaan Republik Indonesia.",
          "3) Program Magang ini mempunyai sistem gugur atau drop out yang berarti apabila PIHAK KEDUA tidak memenuhi kualitas standar yang ditentukan PIHAK PERTAMA maka dinyatakan tidak lulus.",
        ];

        for (const text of pasal2Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(text, labelX, currentY, 15, maxWidth);
          currentY += 5;
        }

        // PASAL 3
        currentY = addPasal(3, "KEWAJIBAN DAN PIHAK PERTAMA", currentY);

        // PASAL 3 content - Bagian 1
        const pasal3Part1Texts = [
          {
            text: "1) PIHAK PERTAMA mempunyai kewajiban sebagai berikut :",
            indent: 0,
          },
          {
            text: "a. Memberi uang saku (UMK) kepada PIHAK KEDUA yang dibayarkan pada akhir bulan berikut melalui transfer yang ditentukan PIHAK PERTAMA, yaitu melalui bank CIMB Niaga",
            indent: 15,
          },
          {
            text: "b. Memberikan pelatihan dan pengetahuan serta menyediakan fasilitas pelatihan kepada PIHAK KEDUA.",
            indent: 15,
          },
          {
            text: "c. Mempekerjakan PIHAK KEDUA sebagai karyawan kontrak PIHAK PERTAMA apabila PIHAK KEDUA dinyatakan lulus mengikuti program ini",
            indent: 15,
          },
        ];

        for (const item of pasal3Part1Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(
            item.text,
            labelX + item.indent,
            currentY,
            15,
            maxWidth - item.indent
          );
          currentY += 5;
        }

        // PASAL 3 content - Bagian 2
        const pasal3Part2Texts = [
          {
            text: "2) PIHAK PERTAMA mempunyai hak sebagai berikut :",
            indent: 0,
          },
          {
            text: "a. Menetapkan kurikulum dan jadwal program magang.",
            indent: 15,
          },
          {
            text: "b. Meminta PIHAK KEDUA untuk melakukan kerja praktek di cabang-cabang atau counter-counter yang ditentukan oleh PIHAK PERTAMA.",
            indent: 15,
          },
          {
            text: "c. Mengevaluasi dan menentukan lulus tidaknya PIHAK KEDUA dan program magang ini.",
            indent: 15,
          },
        ];

        for (const item of pasal3Part2Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(
            item.text,
            labelX + item.indent,
            currentY,
            15,
            maxWidth - item.indent
          );
          currentY += 5;
        }

        // PASAL 4
        currentY = addPasal(4, "KEWAJIBAN DAN HAK PIHAK KEDUA", currentY);

        // PASAL 4 content - Bagian 1
        const pasal4Part1Texts = [
          {
            text: "1) Pihak kedua MEMPUNYAI KEWAJIBAN :",
            indent: 0,
          },
          {
            text: "a. Mentaati peraturan/ketentuan yang ditetapkan oleh PIHAK PERTAMA.",
            indent: 15,
          },
          {
            text: "b. Mengikuti seluruh kegiatan yang ditetapkan PIHAK PERTAMA sampai berakhirnya program magang dan bersedia untuk dievaluasi oleh PIHAK PERTAMA.",
            indent: 15,
          },
        ];

        for (const item of pasal4Part1Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(
            item.text,
            labelX + item.indent,
            currentY,
            15,
            maxWidth - item.indent
          );
          currentY += 5;
        }

        // PASAL 4 content - Bagian 2
        const pasal4Part2Texts = [
          {
            text: "2) PIHAK KEDUA mempunyai hak sebagai berikut :",
            indent: 0,
          },
          {
            text: "a. Mendapatkan pengetahuan dan pelatihan dari PIHAK PERTAMA.",
            indent: 15,
          },
          {
            text: "b. Mendapatkan uang saku sebesar UMK (Upah Minimum Kota/Kabupaten) per bulan sesuai ketentuan berlaku.",
            indent: 15,
          },
          {
            text: "c. Mendapatkan Insentif Sesuai yang berlaku apabila PIHAK KEDUA berhasil memenuhi target yang ditetapkan PIHAK PERTAMA.",
            indent: 15,
          },
          {
            text: "d. Mendapatkan kesempatan untuk menjadi karyawan kontrak PIHAK PERTAMA apabila pada akhir program dinyatakan lulus oleh PIHAK PERTAMA.",
            indent: 15,
          },
        ];

        for (const item of pasal4Part2Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(
            item.text,
            labelX + item.indent,
            currentY,
            15,
            maxWidth - item.indent
          );
          currentY += 5;
        }

        // PASAL 5
        currentY = addPasal(5, "BERAKHIRNYA PERJANJIAN MAGANG", currentY);

        // PASAL 5 content
        const pasal5Texts = [
          {
            text: "Perjanjian magang ini akan berakhir dalam hal :",
            indent: 0,
          },
          {
            text: "a. Berakhirnya waktu yang ditentukan dalam PERJANJIAN MAGANG.",
            indent: 15,
          },
          {
            text: "b. Selama mengikuti program ini PIHAK KEDUA melakukan pelanggaran terhadap peraturan dan ketentuan yang ditetapkan PIHAK PERTAMA.",
            indent: 15,
          },
          {
            text: "c. PIHAK PERTAMA menyatakan bahwa PIHAK KEDUA tidak lulus mengikuti evaluasi.",
            indent: 15,
          },
        ];

        for (const item of pasal5Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(
            item.text,
            labelX + item.indent,
            currentY,
            15,
            maxWidth - item.indent
          );
          currentY += 5;
        }

        // PASAL 6
        currentY = addPasal(6, "PERSELISIHAN", currentY);

        // PASAL 6 content
        const pasal6Texts = [
          {
            text: "1) Dalam hal terjadi perselisihan kedua belah pihak sepakat untuk menyelesaikan secara kekeluargaan untuk mencapai kata sepakat.",
            indent: 0,
          },
          {
            text: "2) Jika dalam musyawarah tidak tercapai kata sepakat maka mengenai perjanjian ini dan segala akibat serta pelaksanaanya para pihak memilih domisili yang umum dan tetap di Pengadilan Hubungan Industrial.",
            indent: 0,
          },
        ];

        for (const item of pasal6Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(
            item.text,
            labelX + item.indent,
            currentY,
            15,
            maxWidth - item.indent
          );
          currentY += 10;
        }

        // Paragraf Penutup
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);
        const penutupText = `Demikian Perjanjian kerja ini ditanda-tangani di Jakarta pada hari ${dateInfo.dayName} tanggal ${dateInfo.date} ${dateInfo.month} ${dateInfo.year} oleh para pihak dalam 2 ( dua ) rangkap bermeterai cukup dan masing-masing mempunyai kekuatan hukum yang sama.`;
        currentY = addMultiLineText(
          penutupText,
          labelX,
          currentY,
          15,
          maxWidth
        );

        // Bagian Tanda Tangan
        const ttdX1 = 90;
        const ttdX2 = 400;
        currentY += 20;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 150);

        doc.setFontSize(11);
        doc.setFont("helvetica", "bold");
        doc.text("PIHAK PERTAMA,", ttdX1, currentY);
        doc.text("PIHAK KEDUA,", ttdX2, currentY);

        const namaY = currentY + 100;
        doc.text(this.activeSignature.name, ttdX1, namaY);
        doc.text(header.profile_name, ttdX2, namaY);

        // Garis bawah nama
        doc.setLineWidth(0.5);
        doc.line(
          ttdX1,
          namaY + 2,
          ttdX1 + doc.getTextWidth(this.activeSignature.name),
          namaY + 2
        );
        doc.line(
          ttdX2,
          namaY + 2,
          ttdX2 + doc.getTextWidth(header.profile_name),
          namaY + 2
        );

        // Jabatan
        const jabatanY = namaY + 15;
        doc.setFont("helvetica", "normal");
        doc.text(this.activeSignature.position, ttdX1, jabatanY);
        doc.text("Karyawan", ttdX2, jabatanY);

        const totalPages = doc.internal.getNumberOfPages();
        for (let i = 1; i <= totalPages; i++) {
          doc.setPage(i);
          await this.addHeaderFooter(doc);
        }

        // Simpan PDF
        const fileName = `Perjanjian_Kerja_${header.no_kontrak || "draft"}.pdf`;
        doc.save(fileName);

        mythis.$root.stopLoading();
        Swal.fire("Success", "PDF has been generated successfully", "success");
      } catch (error) {
        console.error("Error generating PDF:", error);
        mythis.$root.stopLoading();
        Swal.fire("Error", "Failed to generate PDF", "error");
      }
    },

    //async export PDF2 PKWT////////////////////////////////////////////////////////////

    async exportPdf2(kontrakId, dateInfo) {
      const mythis = this;
      mythis.$root.presentLoading();
      await this.getActiveSignature();

      try {
        // Memanggil data kontrak
        const kontrakReq = await axios({
          method: "get",
          url: mythis.$root.apiHost + `api/hr_kontrak/${kontrakId}`,
        });
        const kontrakData = kontrakReq.data.data;
        console.log("Data Kontrak:", kontrakData);

        // Memanggil data profile berdasarkan ID profile
        const profileReq = await axios({
          method: "get",
          url: mythis.$root.apiHost + "api/profilesalldata",
        });
        const profileData = profileReq.data.find(
          (p) => p.profile_name === kontrakData.profiles_id
        );

        if (!profileData) {
          throw new Error(
            `Profile dengan nama ${kontrakData.profiles_id} tidak ditemukan`
          );
        }

        console.log("Data Profile:", profileData);

        const header = {
          ...kontrakData,
          ...profileData,
        };
        console.log("Data Gabungan:", header);

        const doc = new jsPDF("p", "pt", "a4");
        const pageMargins = await this.addHeaderFooter(doc);
        let currentY = pageMargins.headerHeight;

        // Konstanta untuk posisi horizontal
        const labelX = 40;
        const colonX = 180;
        const valueX = 200;
        const indentX = 55;
        const maxWidth = doc.internal.pageSize.getWidth() - 2 * labelX;

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

        // Set judul dengan underline
        currentY += 20;
        doc.setFontSize(16);
        doc.setFont("helvetica", "bold");
        const title = "PERJANJIAN WAKTU TERTENTU";
        const titleWidth =
          doc.getStringUnitWidth(title) * doc.internal.getFontSize();
        const titleX = (doc.internal.pageSize.getWidth() - titleWidth) / 2;
        doc.text(title, doc.internal.pageSize.getWidth() / 2, currentY, {
          align: "center",
        });

        // Menambahkan garis bawah pada judul
        doc.line(titleX, currentY + 2, titleX + titleWidth, currentY + 2);

        // Nomor kontrak tanpa highlight
        doc.setFontSize(11);

        currentY += 20;
        doc.setFontSize(11);
        const noKontrak = `${header.no_kontrak || "No. ??/SGS-HRD/??/2024"}`;
        doc.setTextColor(0, 0, 0);
        doc.text(noKontrak, doc.internal.pageSize.getWidth() / 2, currentY, {
          align: "center",
        });

        // Reset font to normal
        doc.setFont("helvetica", "normal");

        // Fungsi untuk memformat tanggal
        function formatDate(dateString) {
          if (!dateString) return "INPUT OTOMATIS";
          try {
            const cleanDate = dateString.replace(/-/g, "/");
            const dateParts = cleanDate.split("/");
            if (dateParts.length !== 3) return dateString;
            const year = dateParts[0];
            const month = dateParts[1];
            const date = dateParts[2];
            if (month < 1 || month > 12) return dateString;
            const monthName = months[parseInt(month) - 1];
            return `${parseInt(date)} ${monthName} ${year}`;
          } catch (error) {
            return dateString;
          }
        }

        const addMultiLineText = (
          text,
          startX,
          startY,
          lineHeight,
          maxWidth
        ) => {
          doc.setFont("helvetica", "normal");
          // Split text into lines based on maxWidth
          const lines = doc.splitTextToSize(text, maxWidth);

          // Process each line for bold text
          lines.forEach((line, index) => {
            // Find instances of "PIHAK PERTAMA" and "PIHAK KEDUA"
            const words = line.split(/\b(PIHAK PERTAMA|PIHAK KEDUA)\b/);
            let currentX = startX;

            words.forEach((word) => {
              if (word === "PIHAK PERTAMA" || word === "PIHAK KEDUA") {
                doc.setFont("helvetica", "bold");
                doc.text(word, currentX, startY + index * lineHeight);
                const width =
                  (doc.getStringUnitWidth(word) * doc.internal.getFontSize()) /
                  doc.internal.scaleFactor;
                currentX += width;
                doc.setFont("helvetica", "normal");
              } else if (word) {
                doc.text(word, currentX, startY + index * lineHeight);
                const width =
                  (doc.getStringUnitWidth(word) * doc.internal.getFontSize()) /
                  doc.internal.scaleFactor;
                currentX += width;
              }
            });
          });

          return startY + lines.length * lineHeight;
        };

        // Paragraf pembuka
        currentY += 35;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 50);
        doc.text(
          `Pada hari ini ${dateInfo.dayName} Tanggal ${dateInfo.date} Bulan ${dateInfo.month} Tahun ${dateInfo.year} (${dateInfo.date} ${dateInfo.month} ${dateInfo.year}) ditandatangani perjanjian kerja oleh dan antara:`,
          labelX,
          currentY,
          { maxWidth: maxWidth, align: "justify" }
        );

        // Informasi PIHAK PERTAMA
        currentY += 30;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 80);
        doc.text("1. Nama", labelX, currentY);
        doc.text(":", colonX, currentY);
        doc.setFont("helvetica", "bold");
        doc.text(this.activeSignature.name, valueX, currentY);
        doc.setFont("helvetica", "normal");

        currentY += 20;
        doc.text("Jabatan", indentX, currentY);
        doc.text(":", colonX, currentY);
        doc.setFont("helvetica", "bold");
        doc.text(
          `${this.activeSignature.position} PT. Sinergi Global Servis`, // Gunakan template literal
          valueX,
          currentY
        );
        doc.setFont("helvetica", "normal");

        // Paragraf PIHAK PERTAMA
        currentY += 25;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);
        const pihakPertamaText =
          "Dalam hal ini bertindak dalam jabatannya tersebut untuk dan atas nama PT. Sinergi Global Servis, perseroan yang beralamat di Kompleks Buaran Persada No. 26 - 27 Jl. Jend. Pol. R. Sukamto, Duren Sawit, Jakarta Timur, yang selanjutnya disebut PIHAK PERTAMA.";
        doc.text(pihakPertamaText, labelX, currentY, {
          maxWidth: maxWidth,
          align: "justify",
        });

        // Informasi PIHAK KEDUA
        currentY += 50;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 100);
        doc.text("2. Nama", labelX, currentY);
        doc.text(":", colonX, currentY);
        doc.text(header.profile_name || "INPUT OTOMATIS", valueX, currentY);

        currentY += 20;
        doc.text("Tempat, tanggal lahir", indentX, currentY);
        doc.text(":", colonX, currentY);

        const birthInfo = header.birthplace
          ? header.birthplace + ", " + formatDate(header.birthdate)
          : "INPUT OTOMATIS";

        doc.text(birthInfo, valueX, currentY);

        currentY += 20;
        doc.text("No Identitas", indentX, currentY);
        doc.text(":", colonX, currentY);
        doc.text(header.identity_number || "INPUT OTOMATIS", valueX, currentY);

        currentY += 20;
        doc.text("Alamat", indentX, currentY);
        doc.text(":", colonX, currentY);
        doc.text(header.profile_address || "INPUT OTOMATIS", valueX, currentY, {
          maxWidth: maxWidth - valueX + labelX,
        });

        // Paragraf PIHAK KEDUA
        currentY += 30;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
        const pihakKeduaText =
          "Dalam hal ini bertindak untuk dan atas nama diri sendiri, selanjutnya disebut sebagai PIHAK KEDUA.";
        doc.text(pihakKeduaText, labelX, currentY, { maxWidth: maxWidth });

        // Paragraf penutup
        currentY += 20;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
        doc.text(
          "Kedua belah pihak sepakat untuk mengadakan perjanjian kerja dengan syarat dan ketentuan sebagai berikut:",
          labelX,
          currentY,
          { maxWidth: maxWidth, align: "justify" }
        );

        // Fungsi helper untuk menambah pasal
        const addPasal = (number, title, currentY) => {
          currentY += 15;
          currentY = this.checkNewPage(doc, currentY, pageMargins, 65);
          doc.setFont("helvetica", "bold");
          doc.setFontSize(11);
          doc.text(
            `PASAL ${number}`,
            doc.internal.pageSize.getWidth() / 2,
            currentY,
            {
              align: "center",
            }
          );
          return currentY;
        };

        // PASAL 1
        currentY = addPasal(1, "", currentY);

        // PASAL 1 content
        currentY += 25;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);
        const pasal1Text = `PIHAK PERTAMA menerima PIHAK KEDUA terhitung mulai tanggal ${formatDate(
          header.tgl_masuk
        )} sampai dengan ${formatDate(header.tgl_keluar)} sebagai ${
          header.jabatan_id
        } Cab. ${header.cabang_id} ditempatkan di PT. ${header.unit_id}.`;
        currentY = addMultiLineText(pasal1Text, labelX, currentY, 15, maxWidth);

        // PASAL 2
        currentY = addPasal(2, "", currentY);
        currentY += 25;

        // Paragraf RUANG LINGKUP
        const pasal2Texts = [
          {
            text: "(2) PIHAK KEDUA bersedia bekerja dengan ketentuan jam kerja sesuai aturan perusahaan / toko dan melaksanakan perintah dari PIHAK PERTAMA, diantaranya :",
            indent: 0,
          },
          {
            text: "1. Menjual produk-produk yang ada di outlet;",
            indent: 15,
          },
          {
            text: "2. Melakukan edukasi kepada konsumen yang datang ke outlet;",
            indent: 15,
          },
          {
            text: "3. Melaporkan stock bulanan, SOBA, program sell out, foto display counter, foto POP, nota serta data konsumen, foto purchase order (PO), laporan new product launching, dan laporan validasi;",
            indent: 15,
          },
          {
            text: "4. Mengumpulkan absen yang telah diverifikasi outlet kepada AS;",
            indent: 15,
          },
          {
            text: "5. Tidak mengosongkan/ meninggalkan outlet-outlet consignment;",
            indent: 15,
          },
          {
            text: "6. Menjaga ketersediaan tester dan maksimal H-30 hari sebelum habis telah mengajukan permintaan tester;",
            indent: 15,
          },
          {
            text: "7. Menjaga kebersihan counter / outlet, properti i, dan alat-alat kebersihan dari kotoran dan debu;",
            indent: 15,
          },
          {
            text: "8. Mendata seluruh konsumen yang datang;",
            indent: 15,
          },
          {
            text: "9. Memeriksa jumlah stock dan expire date dari setiap produk yang ada di outlet.",
            indent: 15,
          },
          {
            text: "(2) PIHAK KEDUA bersedia bekerja melebihi waktu yang telah ditetapkan apabila diperlukan oleh PIHAK PERTAMA",
            indent: 0,
          },
          {
            text: "(3) PIHAK KEDUA bersedia ditempatkan dimana saja apabila sewaktu-waktu ditugaskan oleh PIHAK PERTAMA",
            indent: 0,
          },
        ];

        for (const item of pasal2Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(
            item.text,
            labelX + item.indent,
            currentY,
            15,
            maxWidth - item.indent
          );
          currentY += 5;
        }

        // PASAL 3
        currentY = addPasal(3, "", currentY);
        currentY += 25;

        const salaryAmount = await this.getSallaryByCabang(header.cabang_id);

        const pasal3Texts = [
          {
            text: "(1) PIHAK PERTAMA memberikan imbalan berupa :",
            indent: 0,
          },
          {
            text: `a. Upah Pokok : ${salaryAmount}`,
            indent: 15,
          },
          {
            text: "b. Insentif : Sesuai peraturan yang berlaku",
            indent: 15,
          },
          {
            text: "(2) Upah yang diberikan untuk PIHAK KEDUA akan dibayarkan pada setiap akhir bulan melalu transfer bank, pada Bank yang sudah ditunjuk oleh PIHAK PERTAMA, yaitu Bank CIMB NIAGA",
            indent: 0,
          },
        ];

        for (const item of pasal3Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(
            item.text,
            labelX + item.indent,
            currentY,
            15,
            maxWidth - item.indent
          );
          currentY += 5;
        }

        // PASAL 4
        currentY = addPasal(4, "", currentY);

        currentY += 25;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);

        const pasal4Texts = [
          {
            text: "(1) PIHAK KEDUA mendapat cuti tahunan selama 12 hari kerja setelah PIHAK KEDUA bekerja selama 12 bulan berturut-turut tanpa alpa atau ijin.",
            indent: 0,
          },
          {
            text: "(2) PIHAK KEDUA wajib memberikan informasi kepada atasan jika terlambat atau tidak masuk kerja. Ijin yang disebabkan sakit maka harus melampirkan surat dokter.",
            indent: 0,
          },
        ];

        for (const item of pasal4Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(
            item.text,
            labelX + item.indent,
            currentY,
            15,
            maxWidth - item.indent
          );
          currentY += 5;
        }

        // PASAL 5
        currentY = addPasal(5, "", currentY);

        currentY += 25;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);

        const pasal5Texts = [
          {
            text: "(1) PIHAK KEDUA mendapat tunjangan Hari Raya ( THR ) 1 bulan Gaji Pokok apabila PIHAK KEDUA telah bekerja selama 12 bulan berturut - turut.",
            indent: 0,
          },
          {
            text: "(2) Apabila PIHAK KEDUA bekerja kurang dari 12 bulan, maka besarnya THR dihitung proporsional dari Gaji Pokok.",
            indent: 0,
          },
        ];

        for (const item of pasal5Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(
            item.text,
            labelX + item.indent,
            currentY,
            15,
            maxWidth - item.indent
          );
          currentY += 5;
        }

        // PASAL 6
        currentY = addPasal(6, "", currentY);

        currentY += 25;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);

        const pasal6Texts = [
          {
            text: "(1) PIHAK PERTAMA dapat memutuskan hubungan kerja dengan PIHAK KEDUA antara lain karena:",
            indent: 0,
          },
          {
            text: "1. Jangka waktu perjanjian telah selesai",
            indent: 10,
          },
          {
            text: "2. PIHAK KEDUA melanggar Tata Tertib Kerja seperti :",
            indent: 10,
          },
          {
            text: "a) Mabuk di tempat kerja;",
            indent: 20,
          },
          {
            text: "b) Terlibat obat bius / obat terlarang;",
            indent: 20,
          },
          {
            text: "c) Menganiaya atau melakukan tindak kekerasan terhadap pengusaha, keluarga pengusaha atasan ataupun sesama pekerja;",
            indent: 20,
          },
          {
            text: "d) Memberikan keterangan palsu;",
            indent: 20,
          },
          {
            text: "e) Menyalahgunakan wewenang jabatan untuk keuntungan/kepentingan pribadi sehingga menimbulkan kerugian perusahaan;",
            indent: 20,
          },
          {
            text: "f) Membuka rahasia perusahaan;",
            indent: 20,
          },
          {
            text: "g) Melakukan tindak pidana seperti mencuri, menipu, atau kejahatan lainnya;",
            indent: 20,
          },
          {
            text: "h) Berjudi di lingkungan perusahaan;",
            indent: 20,
          },
          {
            text: "i) Berbuat / melakukan hal-hal asusila di lingkungan perusahaan;",
            indent: 20,
          },
          {
            text: "j) Mangkir berturut - turut 5 hari kerja dalam satu bulan atau 3 hari kerja tidak berturut - turut dalam satu bulan tanpa keterangan/pemberitahuan kerja;",
            indent: 20,
          },
          {
            text: "k) Melakukan tindakan yang menyebabkan perusahaan mengalami kerugian material;",
            indent: 20,
          },
          {
            text: "l) Melakukan pelanggaran lain yang dapat dipandang setara dengan yang tersebut diatas;",
            indent: 20,
          },
          {
            text: "3. Mengundurkan diri",
            indent: 10,
          },
          {
            text: "4. Tidak mampu menjalankan tugas karena sakit / cacat yang dinyatakan oleh dokter.",
            indent: 10,
          },
          {
            text: "(2) Atas berakhirnya masa perjanjian kerja ini PIHAK KEDUA mengundurkan diri, maka PIHAK KEDUA dikenakan ganti rugi yang harus dibayarkan pada PIHAK PERTAMA sebesar upah yang ditentukan hingga berakhirnya jangka waktu perjanjian kerja.",
            indent: 0,
          },
          {
            text: "(3) Jangka waktu penyelesaian administrasi kekaryawanan PIHAK KEDUA diberikan batas waktu maksimal 2 (dua) bulan sejak tanggal dinyatakan Status FTE/FTI.",
            indent: 0,
          },
        ];

        for (const item of pasal6Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(
            item.text,
            labelX + item.indent,
            currentY,
            15,
            maxWidth - item.indent
          );
          currentY += 5;
        }

        // PASAL 7 header
        currentY = addPasal(7, "", currentY);
        currentY += 25;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);

        const pasal7Texts = [
          {
            text: "(1) PIHAK PERTAMA dapat memberikan teguran/ surat peringatan I,II,III/ schorsing/ pemutusan hubungan kerja dengan PIHAK KEDUA sesuai dengan tingkat pelanggarannya apabila PIHAK KEDUA melakukan pelanggaran terhadap Perjanjian Kerja/Waktu Internal/Peraturan Perusahaan/ Undang-undang Ketenagakerjaan.",
            indent: 0,
          },
          {
            text: "(2) Apabila PIHAK KEDUA melakukan kelalaian sehingga mengakibatkan kerugian pada perusahaan dimana PIHAK KEDUA ditempatkan, maka kerugian dimaksud menjadi tanggung jawab PIHAK KEDUA, termasuk hal-hal yang berkaitan dengan selisih stock barang di outlet dan juga stock yang sudah mencapai expire date.",
            indent: 0,
          },
        ];

        for (const item of pasal7Texts) {
          currentY = this.checkNewPage(doc, currentY, pageMargins, 40);
          currentY = addMultiLineText(
            item.text,
            labelX + item.indent,
            currentY,
            15,
            maxWidth - item.indent
          );
          currentY += 5;
        }

        // PASAL 8
        currentY = addPasal(8, "", currentY);
        currentY += 25;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);

        const pasal8Text =
          "PIHAK KEDUA wajib mengembalikan semua aset milik perusahaan termasuk seragam bekerja dan menyelesaikan perjanjian lain selama bekerja, pada saat PIHAK KEDUA berhenti kerja. Jika terjadi kerusakan/ cacat dan merobah model pada seragam maka PIHAK KEDUA akan dikenakan biaya sebesar Rp. 750.000,- (tujuh ratus lima puluh ribu rupiah) sebagai bentuk tanggung jawab.";
        currentY = addMultiLineText(pasal8Text, labelX, currentY, 15, maxWidth);

        // PASAL 9
        currentY = addPasal(9, "", currentY);
        currentY += 25;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);

        const pasal9Text =
          "Selama bekerja, PIHAK KEDUA bersedia wajib mematuhi peraturan Perusahaan dan perintah-perintah kerja yang layak dari Pejabat Perusahaan.";
        currentY = addMultiLineText(pasal9Text, labelX, currentY, 15, maxWidth);

        // PASAL 10
        currentY = addPasal(10, "", currentY);
        currentY += 25;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);

        const pasal10Text =
          "Apabila terdapat hal – hal yang belum tercantum diatas, maka pelaksanaannya akan mengacu kepada Undang – Undang Republik Indonesia No. 13 Tahun 2003 tentang Ketenagakerjaan.";
        currentY = addMultiLineText(
          pasal10Text,
          labelX,
          currentY,
          15,
          maxWidth
        );

        // Paragraf Penutup
        currentY += 20;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 60);
        const penutupText = `Demikian Perjanjian kerja ini ditanda-tangani di Jakarta pada hari ${dateInfo.dayName} tanggal ${dateInfo.date} ${dateInfo.month} ${dateInfo.year} oleh para pihak dalam 2 ( dua ) rangkap bermeterai cukup dan masing-masing mempunyai kekuatan hukum yang sama.`;
        currentY = addMultiLineText(
          penutupText,
          labelX,
          currentY,
          15,
          maxWidth
        );

        // Bagian Tanda Tangan
        const ttdX1 = 90;
        const ttdX2 = 400;
        currentY += 20;
        currentY = this.checkNewPage(doc, currentY, pageMargins, 150);

        doc.setFontSize(11);
        doc.setFont("helvetica", "bold");
        doc.text("PIHAK PERTAMA,", ttdX1, currentY);
        doc.text("PIHAK KEDUA,", ttdX2, currentY);

        const namaY = currentY + 100;
        doc.text(this.activeSignature.name, ttdX1, namaY);
        doc.text(header.profile_name, ttdX2, namaY);

        // Garis bawah nama
        doc.setLineWidth(0.5);
        doc.line(
          ttdX1,
          namaY + 2,
          ttdX1 + doc.getTextWidth(this.activeSignature.name),
          namaY + 2
        );
        doc.line(
          ttdX2,
          namaY + 2,
          ttdX2 + doc.getTextWidth(header.profile_name),
          namaY + 2
        );

        // Jabatan
        const jabatanY = namaY + 15;
        doc.setFont("helvetica", "normal");
        doc.text(this.activeSignature.position, ttdX1, jabatanY);
        doc.text("Karyawan", ttdX2, jabatanY);

        const totalPages = doc.internal.getNumberOfPages();
        for (let i = 1; i <= totalPages; i++) {
          doc.setPage(i);
          await this.addHeaderFooter(doc);
        }

        // Simpan PDF
        const fileName = `Perjanjian_Kerja_${header.no_kontrak || "draft"}.pdf`;
        doc.save(fileName);

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
            "api/hr_kontrak?offset=" +
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
            cabang_id: resData.results[key].cabang_id,
            no_kontrak: resData.results[key].no_kontrak,
            tipe: resData.results[key].tipe,
            tgl_masuk: resData.results[key].tgl_masuk,
            tgl_keluar: resData.results[key].tgl_keluar,
            tahun: resData.results[key].tahun,
            bulan: resData.results[key].bulan,
            hari: resData.results[key].hari,
            tgl_take_out: resData.results[key].tgl_take_out,
            tgl_make_kontrak: resData.results[key].tgl_make_kontrak,
            ket: resData.results[key].ket,
            ket_take_out: resData.results[key].ket_take_out,
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
        cabang_id: mythis.formatDate(new Date()),
      };
      mythis.data_x_excel[baris_excel] = countries_x;

      mythis.json_data = mythis.data_x_excel;
      mythis.flagDownloadXLSX = 1;

      var a = new Date().toLocaleString("en-GB");
      mythis.nama_excelnya = "Data_Kontrak " + a + ".xlsx";
      // mythis.nama_sheetnya = mythis.nama_excelnya;

      mythis.$root.stopLoading();
    },

    download_excel_xyz() {},
    async startDownload() {
      await this.getDataExportExcel();
    },
    finishDownload() {},

    setTanggalKeluar() {
      // Pastikan tgl_masuk dan selectedTimeframe terisi
      if (this.todo.tgl_masuk && this.selectedTimeframe) {
        console.log("Selected Timeframe:", this.selectedTimeframe);

        // Ambil tanggal masuk dan buat sebagai objek Date
        let tglMasuk = new Date(this.todo.tgl_masuk);
        let tglKeluar = new Date(this.todo.tgl_masuk);

        // Jika yang dipilih adalah bulan
        if (this.selectedTimeframe.type === "month") {
          // Tambahkan bulan ke tanggal keluar
          tglKeluar.setMonth(
            tglKeluar.getMonth() + this.selectedTimeframe.value
          );
        }
        // Jika yang dipilih adalah tahun
        else if (this.selectedTimeframe.type === "year") {
          // Tambahkan tahun ke tanggal keluar
          tglKeluar.setFullYear(
            tglKeluar.getFullYear() + this.selectedTimeframe.value
          );
        }

        // Kurangi 1 hari dari tanggal keluar
        tglKeluar.setDate(tglKeluar.getDate() - 1);

        // Format tgl_keluar ke format YYYY-MM-DD
        this.todo.tgl_keluar = tglKeluar.toISOString().split("T")[0];

        // Set nilai tahun, bulan dan hari sesuai selectedTimeframe
        if (this.selectedTimeframe.type === "month") {
          this.todo.tahun = 0; // 0 tahun
          this.todo.bulan = this.selectedTimeframe.value; // Jumlah bulan yang dipilih
          this.todo.hari = 0; // 0 hari
        } else {
          this.todo.tahun = this.selectedTimeframe.value; // Jumlah tahun yang dipilih
          this.todo.bulan = 0; // 0 bulan
          this.todo.hari = 0; // 0 hari
        }

        // Debugging untuk memastikan hasil perhitungan
        console.log(
          "Tahun:",
          this.todo.tahun,
          "Bulan:",
          this.todo.bulan,
          "Hari:",
          this.todo.hari
        );
      }
    },

    async checkTotalContractYears(profileId) {
      try {
        // Panggil API untuk mendapatkan total masa kontrak dalam tahun untuk profile_id
        const response = await axios.get(
          this.$root.apiHost + `api/totalContractYears?profiles_id=${profileId}`
        );

        // Mengembalikan total masa kontrak dalam tahun
        return response.data.totalYears;
      } catch (error) {
        console.error("Error fetching total contract years:", error);
        return 0; // Jika terjadi error, anggap total masa kontrak adalah 0
      }
    },

    async getKontrakCountPKWT(profileId) {
      try {
        // Panggil API untuk mendapatkan jumlah kontrak PKWT yang sudah ada untuk profile_id ini
        const response = await axios.get(
          this.$root.apiHost +
            "api/kontrak-count?profile_id=${profileId}&tipe=PKWT"
        );

        return response.data.count || 1; // Jika count tidak ditemukan, anggap ini kontrak pertama
      } catch (error) {
        console.error("Error fetching kontrak count:", error);
        return 1; // Default jika ada error, anggap kontrak pertama
      }
    },

    async generateNoKontrak() {
      try {
        const today = new Date();
        const month = today.getMonth() + 1;
        const year = today.getFullYear();

        // Log untuk debugging
        console.log("Current todo:", this.todo);
        console.log("temp_profile_id:", this.todo.temp_profile_id);

        if (!this.todo.temp_profile_id) {
          console.error("temp_profile_id is empty");
          return;
        }

        // Konversi bulan ke angka Romawi
        const romanMonth = this.convertToRoman(month);

        if (this.todo.tipe === "PKWT") {
          // Buat FormData untuk mengirim data
          const formData = new FormData();
          formData.append("temp_profile_id", this.todo.temp_profile_id);
          formData.append("tipe", "PKWT");

          console.log("Sending request with:", {
            temp_profile_id: this.todo.temp_profile_id,
            tipe: "PKWT",
          });

          const response = await axios.post(
            this.$root.apiHost + "api/hr_kontrak/getLastContractNumber",
            {
              temp_profile_id: this.todo.temp_profile_id,
              tipe: "PKWT",
            },
            {
              headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
              },
            }
          );

          console.log("Response:", response.data);
          const kontrakKe = response.data.count;
          this.todo.no_kontrak = `No. ${this.todo.temp_profile_id}-${kontrakKe}/SGS-HR/${romanMonth}/${year}`;
        } else if (this.todo.tipe === "MAGANG") {
          // Perbaikan di sini
          this.todo.no_kontrak = `No. ${this.todo.temp_profile_id}/SGS-HR/${romanMonth}/${year}`;
        }

        // Log nomor kontrak yang dihasilkan
        console.log("Generated no_kontrak:", this.todo.no_kontrak);
      } catch (error) {
        console.error(
          "Error in generateNoKontrak:",
          error.response ? error.response.data : error
        );
        toast.error("Gagal generate nomor kontrak", { theme: "colored" });
      }
    },
    async onChangeProfile() {
      try {
        console.log("Selected Profile:", this.selectedProfile);

        if (!this.selectedProfile) {
          this.todo.profiles_id = "";
          this.todo.temp_profile_id = "";
          return;
        }

        // Update todo with profile data
        this.todo.profiles_id = this.selectedProfile.profile_name;
        this.todo.temp_profile_id = this.selectedProfile.id;

        console.log("Updated todo:", {
          profiles_id: this.todo.profiles_id,
          temp_profile_id: this.todo.temp_profile_id,
        });

        // Get last exit date
        await this.getLastExitDate(this.todo.profiles_id);

        // If type is already selected, generate contract number
        if (this.todo.tipe) {
          await this.generateNoKontrak();
        }
      } catch (error) {
        console.error("Error in onChangeProfile:", error);
        toast.error("Gagal memilih karyawan", { theme: "colored" });
      }
    },
    async getLastExitDate(profileId) {
      try {
        // Panggil API untuk mendapatkan tanggal keluar terakhir
        const response = await axios.get(
          this.$root.apiHost + `api/lastExitDate?profiles_id=${profileId}`
        );

        if (response.data && response.data.tgl_keluar) {
          // Jika ada tanggal keluar terakhir, tambah 1 hari
          let lastExitDate = new Date(
            new Date(response.data.tgl_keluar).setDate(
              new Date(response.data.tgl_keluar).getDate() + 1
            )
          )
            .toISOString()
            .split("T")[0];

          // Set tanggal masuk dengan tanggal keluar + 1 hari
          this.todo.tgl_masuk = lastExitDate;
        } else {
          // Jika tidak ada kontrak sebelumnya, kosongkan tanggal masuk
          this.todo.tgl_masuk = "";
        }
      } catch (error) {
        console.error("Error fetching last exit date:", error);
      }
    },

    async getProfile() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/profilesalldata"
        );
        this.profiles = response.data; // Menyimpan data profiles ke dalam `profiles`
      } catch (error) {
        console.error("Error fetching profiles:", error);
      }
    },

    async getProfileByCabang(nama_cabang) {
      try {
        const response = await axios.get(
          `${this.$root.apiHost}api/profilesalldata?nama_cabang=${nama_cabang}`
        );
        this.profiles = response.data;
        console.log("Loaded profiles for cabang:", this.profiles);
      } catch (error) {
        console.error("Error fetching profiles:", error);
        toast.error("Gagal mengambil data karyawan", { theme: "colored" });
      }
    },

    async getProfilePdf() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/kontrakalldata"
        );
        // console.log(response.data)
        this.profilespdf = response.data; // Menyimpan data profiles ke dalam `profiles`
        this.noKontrakList = response.data;
      } catch (error) {
        console.error("Error fetching profiles:", error);
      }
    },

    // Fungsi untuk handle perubahan nomor kontrak
    onChangeKontrakPdf() {
      this.todopdf.no_kontrak = this.selectedNoKontrak.no_kontrak;
    },

    onChangeProfilePdf() {
      this.todopdf.profiles_id = this.selectedProfilePdf.profiles_id;
      //this.getProfilePdf(selectedProfilePdf.profile_id); // Panggil fungsi untuk fetch nomor kontrak
    },

    async onChangeCabang(selectedNamaCabang) {
      this.todo.cabang_id = "";
      this.todo.profiles_id = "";
      this.profiles = [];
      if (selectedNamaCabang) {
        if (selectedNamaCabang) {
          // Set cabang_id menggunakan ID
          this.todo.cabang_id = selectedNamaCabang; // Menggunakan nama_cabang untuk pencarian
          await this.getProfileByCabang(selectedNamaCabang);
        }
      }
    },

    async getCabang() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/cabangalldata"
        );
        // Map data untuk memastikan struktur yang benar
        this.cabangs = response.data.map((cabang) => ({
          id: cabang.id,
          nama_cabang: cabang.nama_cabang,
        }));

        console.log("Loaded cabangs:", this.cabangs);
      } catch (error) {
        console.error("Error fetching cabangs:", error);
        toast.error("Gagal mengambil data cabang", { theme: "colored" });
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
      mythis.selectedTimeframe = "";
      mythis.todo.tgl_take_out = null;
      mythis.selectedProfile = "";
      mythis.temp_profile_id = "";
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
    async saveTodo() {
      try {
        // Tampilkan konfirmasi sebelum menyimpan data kontrak
        const result = await Swal.fire({
          title: "Create Data Kontrak",
          text: "Are you sure?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes",
          cancelButtonText: "Cancel",
        });

        // Jika pengguna mengonfirmasi, lanjutkan penyimpanan
        if (result.isConfirmed) {
          this.$root.presentLoading();
          this.$root.flagButtonLoading = true;

          const url = this.$root.apiHost + "api/hr_kontrak";

          // Lanjutkan ke penyimpanan data kontrak
          const response = await axios.post(url, {
            profiles_id: this.todo.profiles_id,
            cabang_id: this.todo.cabang_id,
            no_kontrak: this.todo.no_kontrak,
            tipe: this.todo.tipe,
            tgl_masuk: this.todo.tgl_masuk,
            tgl_keluar: this.todo.tgl_keluar,
            tahun: this.todo.tahun, // Tahun hasil perhitungan
            bulan: this.todo.bulan, // Bulan hasil perhitungan
            hari: this.todo.hari, // Hari hasil perhitungan
            tgl_take_out: this.todo.tgl_take_out,
            ket: this.todo.ket,
            userid: this.userid,
            profile_name: this.todo.profile_name,
          });

          // Tampilkan pesan berhasil dan reset form
          Swal.fire("Created!", response.data.message, "success");
          this.$root.stopLoading();
          this.$root.flagButtonLoading = false;
          this.resetForm();
          this.show_modal();
          this.refreshTable();
        }
      } catch (error) {
        // Berhentikan loading jika ada error
        this.$root.flagButtonLoading = false;
        this.$root.stopLoading();

        // Tangani error response
        if (error.response) {
          if (error.response.status === 422) {
            this.errorList = error.response.data;
            if (Object.keys(this.errorList).length > 0) {
              Object.keys(this.errorField).forEach((key) => {
                this.errorField[key] = false;
              });
              Object.keys(this.errorList).forEach((key) => {
                toast.error(this.errorList[key], { theme: "colored" });
                this.errorField[key] = true;
              });
            }
          } else {
            toast.error(error.response.data.message, { theme: "colored" });
          }
        } else if (error.request) {
          console.error("Request Error:", error.request);
        } else {
          console.error("Error:", error.message);
        }
      }
    },

    show_modal() {
      this.modal = !this.modal;
      if (this.modal === false) {
        this.flagButtonAdd = true;
        this.resetForm();
        this.selectedcabangs = null;
      }
    },
    show_modalpdf() {
      this.modalpdf = !this.modalpdf;
      if (this.modalpdf === false) {
        this.getProfilePdf();
        this.resetForm();
      }
    },

    async jqueryDelEdit() {
      const mythis = this;

      // $(document).off("click", "#editData");
      $(document).off("click", "#deleteData");
      $(document).off("click", "#takeOutData");
      $(document).off("click", ".exportPdfBtn");

      // $(document).on("click", "#editData", async function () {
      //   let id = $(this).data("id");
      //   await mythis.getData(id);
      //   mythis.show_modal();
      // });

      $(document).on("click", "#deleteData", function () {
        let id = $(this).data("id");
        mythis.deleteTodo(id);
      });

      $(document).on("click", "#takeOutData", function () {
        const id = $(this).data("id");
        const profileId = $(this).data("profile");
        mythis.showTakeOutForm(id, profileId);
      });

      // Tambahkan event handler untuk export PDF
      $(document).on("click", ".exportPdfBtn", function (e) {
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
            $("#tanggalModal").modal("show");
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
        // language: idID,
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
        url: (prev, keyword) => {
          // Periksa apakah selectedFilterCabang ada nilainya
          if (this.selectedFilterCabang) {
            return `${prev}${prev.includes("?") ? "&" : "?"}search=${keyword}&cabang_id=${encodeURIComponent(this.selectedFilterCabang.nama_cabang)}`;
          } else {
            return `${prev}${prev.includes("?") ? "&" : "?"}search=${keyword}`;
          }
        },
      },
    },
        columns: [
          { name: "ID", hidden: true },
          "No",
          "Nama Karyawan",
          "Cabang",
          "No Kontrak",
          "Tipe",
          "Kontrak Awal",
          "Kontrak Berakhir",
          {
            name: "Tanggal Download PDF",
            formatter: (cell) => cell || "-",
          },
          {
            name: "Tahun",
            formatter: (cell) => cell || "00", // Pastikan tampilkan "00" jika tahun kosong
          },
          {
            name: "Bulan",
            formatter: (cell) => cell || "00", // Pastikan tampilkan "00" jika bulan kosong
          },
          {
            name: "Hari",
            formatter: (cell) => cell || "00", // Pastikan tampilkan "00" jika hari kosong
          },
          "Tanggal Take Out",
          "Keterangan",
          {
            name: "Keterangan Take Out", // Add this column
            formatter: (cell) => cell || "-",
          },
          {
            name: "Action",
            formatter: (_, row) => {
              const exportButton = `
      <button
  onclick='handleExportClick("${row.cells[0].data}", "${row.cells[5].data}")'
  class="btn btn-sm btn-outline-danger"
  style="margin-right: 5px; background-color: white;"
  title="Cetak PDF">
  <i class="fa fa-file-pdf-o" style="color: red;"></i>
</button>
    `;

              const takeOutButton = `
      <button
        data-id="${row.cells[0].data}"
        data-profile="${row.cells[2].data}"
        class="btn btn-sm btn-warning text-white"
        id="takeOutData"
        data-toggle="tooltip"
        title="Keluar Karyawan"
        style="margin-right: 5px;">
        <i class="fa fa-sign-out"></i>
      </button>
    `;

              const deleteButton = `
      <button
        data-id="${row.cells[0].data}"
        class="btn btn-sm btn-danger text-white"
        id="deleteData"
        data-toggle="tooltip"
        title="Delete Data"
        style="margin-right: 5px;">
        <i class="fa fa-trash"></i>
      </button>
    `;

              return html(`${exportButton}${takeOutButton}${deleteButton}`);
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
          url: this.$root.apiHost + "api/hr_kontrak",
          then: (data) => {
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

            return data.results.map((card) => [
              card.id,
              data.nomorBaris++ + 1,
              html(`<span class="pull-left">${card.profiles_id}</span>`),
              html(`<span class="pull-left">${card.cabang_id}</span>`),
              html(`<span class="pull-left">${card.no_kontrak}</span>`),
              html(`<span class="pull-left">${card.tipe}</span>`),
              html(`<span class="pull-left">${card.tgl_masuk}</span>`),
              html(`<span class="pull-left">${card.tgl_keluar}</span>`),
              html(
                `<span class="pull-left">${card.tgl_make_kontrak || "-"}</span>`
              ),
              html(`<span class="pull-left">${card.tahun || "00"}</span>`), // Tahun
              html(`<span class="pull-left">${card.bulan || "00"}</span>`), // Bulan
              html(`<span class="pull-left">${card.hari || "00"}</span>`), // Hari
              html(
                `<span class="pull-left">${card.tgl_take_out || "-"}</span>`
              ),
              html(`<span class="pull-left">${card.ket}</span>`),
              html(
                `<span class="pull-left">${card.ket_take_out || "-"}</span>`
              ),
            ]);
          },
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
      $(document).off("click", "#exportPdf1");
      $(document).off("click", "#exportPdf2");

      $(document).on("click", "#exportPdf1", function () {
        let id = $(this).data("id");
        mythis.exportPdf1(id); // Pass the transaction ID to export PDF
      });
      $(document).on("click", "#exportPdf2", function () {
        let id = $(this).data("id");
        mythis.exportPdf2(id); // Pass the transaction ID to export PDF
      });

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
            .delete(mythis.$root.apiHost + `api/hr_kontrak/${id}`, config)
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
      mythis.$root.stopLoading();
      // const AuthStr = "bearer " + localStorage.getItem("token");
      const config = {
        // headers: {
        //   Authorization: AuthStr,
        // },
      };
      axios
        .put(
          mythis.$root.apiHost + "api/hr_kontrak/" + mythis.todo.id,
          {
            profiles_id: mythis.todo.profiles_id,
            cabang_id: mythis.todo.cabang_id,
            no_kontrak: mythis.todo.no_kontrak,
            tipe: mythis.todo.tipe,
            tgl_masuk: mythis.todo.tgl_masuk,
            tahun: mythis.todo.tahun,
            bulan: mythis.todo.bulan,
            hari: mythis.todo.hari,
            ket: mythis.todo.ket,
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
        .get(mythis.$root.apiHost + `api/hr_kontrak/${id}`, config)
        .then(async (res) => {
          //console.log(res.data.data);
          //mythis.acuanEdit = id;
          //mythis.todo = res.data.data;
          mythis.todo.id = id;
          mythis.todo.profiles_id = res.data.data.profiles_id;
          mythis.todo.cabang_id = res.data.data.cabang_id;
          mythis.todo.no_kontrak = res.data.data.no_kontrak;
          mythis.todo.tipe = res.data.data.tipe;
          mythis.todo.tgl_masuk = res.data.data.tgl_masuk;
          mythis.todo.tgl_keluar = res.data.data.tgl_keluar;
          mythis.todo.tahun = res.data.data.tahun;
          mythis.todo.bulan = res.data.data.bulan;
          mythis.todo.hari = res.data.data.hari;
          mythis.todo.ket = res.data.data.ket;

          document.getElementById("inputA").focus(); // sets the focus on the input

          mythis.$root.stopLoading();
        });
    },
  },
};
</script>

<style scoped>
/* Card Styles */
.contract-card {
  background: #fff;
  transition: all 0.3s ease;
  border-radius: 8px;
  border: 1px solid #dee2e6;
  margin-bottom: 1rem;
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

/* Hover Effects */
.contract-card:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.bg-danger-light:hover {
  background-color: #ffcdd2 !important;
}

.bg-warning-light:hover {
  background-color: #ffe0b2 !important;
}

.bg-success-light:hover {
  background-color: #c8e6c9 !important;
}

.bg-secondary-light:hover {
  background-color: #e9ecef !important;
}

/* Grid Layout */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.info-item {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
}

/* Badge Styles */
.badge {
  padding: 8px 12px;
  font-size: 14px;
  font-weight: 600;
  border-radius: 4px;
}

.badge-primary {
  background-color: #007bff;
  color: white;
}
.badge-success {
  background-color: #28a745;
  color: white;
}
.badge-warning {
  background-color: #ffc107;
  color: black;
}
.badge-danger {
  background-color: #dc3545;
  color: white;
}
.badge-secondary {
  background-color: #6c757d;
  color: white;
}

/* Button Styles */
.btn {
  border-radius: 6px;
  padding: 0.5rem 1rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn i {
  font-size: 0.9rem;
}

.table-responsive {
  min-height: 200px; /* Minimal tinggi tabel */
  max-height: calc(90vh - 300px); /* Maksimal tinggi tabel */
  overflow: auto;
}

/* Modal Styles */
.modal-content {
  border: none;
  border-radius: 8px;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
  max-height: calc(90vh - 60px); /* Kurangi dengan margin modal */
  overflow: auto;
}

#alertKontrakModal .modal-header,
#tanggalModal .modal-header {
  background-color: #dc3545;
  color: white;
  border-radius: 6px 6px 0 0;
  padding: 1rem 1.5rem;
}

/* Header untuk modal Add Data (Biru) */
#exampleModalCenter .modal-header {
  background-color: #007bff;
  color: white;
  border-radius: 6px 6px 0 0;
  padding: 1rem 1.5rem;
}

/* Style untuk close button di semua modal */
.modal-header .close {
  color: white;
  opacity: 0.8;
  transition: opacity 0.2s ease;
}

.modal-header .close:hover {
  opacity: 1;
}

/* Modal body padding yang konsisten */
.modal-body {
  overflow-x: auto; /* Tambahkan horizontal scroll jika tabel terlalu lebar */
  padding: 15px;
  max-height: calc(90vh - 200px);
}

/* Form Styles */
.form-control {
  border-radius: 6px;
  border: 2px solid #dee2e6;
  padding: 0.6rem 1rem;
  transition: all 0.2s ease;
}

.form-control:focus {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-control.is-invalid {
  border-color: #dc3545;
  background-image: none;
}

.invalid-feedback {
  color: #dc3545;
  font-size: 0.875rem;
}

/* Export Button Styles */
.btn-export {
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 0.5rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-export:hover:not(:disabled) {
  background-color: #c82333;
}

.btn-export:disabled {
  background-color: #e4606d;
  cursor: not-allowed;
}

.btn-export i {
  font-size: 1rem;
}

/* Modal Animation */
.modal.fade.in {
  animation: modalFadeIn 0.3s ease-out;
}

.modal-footer {
  position: sticky; /* Header & footer tetap di posisinya */
  background: white;
  z-index: 1;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Input Error State */
.input-error {
  border: 2px solid #dc3545;
}

/* Loading Spinner */
.fa-spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .modal-dialog {
    margin: 0.5rem;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .badge {
    padding: 6px 10px;
    font-size: 12px;
  }
}

.d-flex {
  display: flex;
}

.align-items-start {
  align-items: flex-start;
}

.ml-2 {
  margin-left: 2px;
}

.d-inline-block {
  display: inline-block;
  width: 250px; /* Adjust width as needed */
  vertical-align: middle;
}

/* Style the v-select component */
:deep(.v-select) {
  background-color: white;
  border-radius: 4px;
  font-size: 14px;
  z-index: 1030;
}

/* Style untuk modal */
.modal {
  z-index: 1050 !important; /* Pastikan modal selalu di atas */
}

.modal-backdrop {
  z-index: 1040 !important;
}

/* Style untuk filter container */
.filter-container {
  position: relative;
  z-index: 1030; /* Di bawah modal tapi di atas konten normal */
}

/* Pastikan wrapper memiliki posisi relatif */
#wrapper2 {
  position: relative;
  z-index: 1;
}

/* Style untuk search dan pagination */
.gridjs-search {
  z-index: 1020;
}

.gridjs-pagination {
  z-index: 1020;
}
</style>
