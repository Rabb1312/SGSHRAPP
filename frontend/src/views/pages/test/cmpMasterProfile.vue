divdiv
<template>
  <!-- CSV Import Modal -->
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
  <!---------------------------- Modal -->
  <div
    v-if="modal"
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
            @click="show_modal(true)"
          >
            <span aria-hidden="true">X</span>
          </button>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <a
              class="nav-link"
              :class="{ active: activeTab === 'informasi' }"
              @click.prevent="setActiveTab('informasi')"
              href="#"
            >
              Informasi
            </a>
          </li>
          <li class="nav-item">
            <a
              class="nav-link"
              :class="{ active: activeTab === 'kontak' }"
              @click.prevent="setActiveTab('kontak')"
              href="#"
            >
              Kontak
            </a>
          </li>
          <li class="nav-item">
            <a
              class="nav-link"
              :class="{ active: activeTab === 'pribadi' }"
              @click.prevent="setActiveTab('pribadi')"
              href="#"
            >
              Informasi Pribadi
            </a>
          </li>
          <li class="nav-item">
            <a
              class="nav-link"
              :class="{ active: activeTab === 'status' }"
              @click.prevent="setActiveTab('status')"
              href="#"
            >
              Status Aktif
            </a>
          </li>
          <li class="nav-item">
            <a
              class="nav-link"
              :class="{ active: activeTab === 'pendukung' }"
              @click.prevent="setActiveTab('pendukung')"
              href="#"
            >
              Data Pendukung
            </a>
          </li>
        </ul>

        <div class="modal-body">
          <!-- <pre>{{ todo }}</pre> -->
          <!-- Wizards Row -->
          <div v-show="activeTab === 'informasi'" class="tab-pane active">
            <div class="card">
              <div class="card-header">
                <h6>Informasi</h6>
              </div>
              <div class="card-body">
                <!-- <div class="col-md-6">
                  <div class="form-group">
                    <label for="example-nf-email">User ID*</label>
                    <CmpInputText
                      id="inputA"
                      type="text"
                      placeholder="User ID"
                      v-model="todo.users_id"
                      :class="
                        errorField.users_id
                          ? 'form-control input-lg input-error'
                          : 'form-control input-lg'
                      "
                      @input="
                        (val) => (todo.users_id = todo.users_id.toUpperCase())
                      "
                    />
                  </div>
                </div> -->
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">Cabang*</label>
                        <v-select
                          variant="outlined"
                          label="nama_cabang"
                          name="nama_cabang"
                          :options="cabangreal"
                          placeholder="Pilih Cabang"
                          v-model="selectedcabangreal"
                          :reduce="(option) => option"
                          @update:modelValue="onChangeCabangReal"
                        />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">Penempatan*</label>
                        <v-select
                          variant="outlined"
                          label="nama_cabang"
                          name="nama_cabang"
                          :options="cabangs"
                          placeholder="Pilih Penempatan"
                          v-model="selectedcabang"
                          @update:modelValue="onChangeCabang"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">Jabatan*</label>
                        <v-select
                          v-model="todo.jabatan_id"
                          id="jabatan_id"
                          variant="outlined"
                          label="jabatan"
                          name="jabatan"
                          :options="jabatans"
                          placeholder="Pilih Jabatan"
                          @update:modelValue="onChangeJabatan"
                        >
                        </v-select>
                      </div>
                    </div>
                    <!-- :disabled="!flagButtonAdd" -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">Unit*</label>
                        <v-select
                          id="unit_id"
                          variant="outlined"
                          label="unit_name"
                          name="unit_name"
                          :options="units"
                          placeholder="Pilih Unit"
                          v-model="selectedunit"
                          :reduce="(option) => option"
                          @update:modelValue="onChangeUnit"
                        >
                        </v-select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email"
                          >NIK (Nomor Induk Karyawan)</label
                        >
                        <CmpInputText
                          id="inputNIK"
                          type="text"
                          placeholder="Auto Generate"
                          v-model="todo.nik"
                          :class="
                            errorField.nik
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          readonly
                        />
                      </div>
                    </div>
                    <!-- :disabled="!flagButtonAdd" -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="join-date-input">Tanggal Join*</label>
                        <input
                          id="join-date-input"
                          type="date"
                          v-model="todo.join_date"
                          :class="
                            errorField.join_date
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          @change="onChangeJoinDate"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-show="activeTab === 'kontak'" class="tab-pane">
            <div class="card mt-3">
              <div class="card-header">
                <h6>Kontak</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">Nomor Identitas*</label>
                        <CmpInputText
                          id="inputA"
                          type="number"
                          placeholder="Nomor Identitas"
                          v-model="todo.identity_number"
                          :class="
                            errorField.identity_number
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          @input="onNIKInput($event, 'identity_number', 16)"
                          :min="0"
                          :max="9999999999999999"
                        />
                      </div>
                    </div>
                    <!-- :disabled="!flagButtonAdd" -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">Nama Karyawan*</label>
                        <CmpInputText
                          type="text"
                          placeholder="Nama Karyawan"
                          v-model="todo.profile_name"
                          :class="
                            errorField.profile_name
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          @input="
                            (val) =>
                              (todo.profile_name =
                                todo.profile_name.toUpperCase())
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
                        <label for="profile-gender">Jenis Kelamin*</label>
                        <select
                          id="profile-gender"
                          v-model="todo.profile_gender"
                          :class="
                            errorField.profile_gender
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                        >
                          <option value="" disabled selected>
                            Pilih Gender
                          </option>
                          <option value="Laki-laki">Laki-laki</option>
                          <option value="Perempuan">Perempuan</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">Kode Pos*</label>
                        <CmpInputText
                          id="inputA"
                          type="number"
                          placeholder="Kode Pos"
                          v-model="todo.postalcode"
                          :class="
                            errorField.postalcode
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          @input="onInput"
                          :min="0"
                          :max="99999"
                        />
                      </div>
                    </div>
                    <!-- :disabled="!flagButtonAdd" -->
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="example-nf-email">Alamat*</label>
                        <textarea
                          id="inputA"
                          type="textarea"
                          placeholder="Alamat"
                          v-model="todo.profile_address"
                          :class="
                            errorField.profile_address
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          @input="
                            (val) =>
                              (todo.profile_address =
                                todo.profile_address.toUpperCase())
                          "
                          rows="4"
                        />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">Email*</label>
                        <CmpInputText
                          type="text"
                          placeholder="Email"
                          v-model="todo.email"
                          :class="
                            errorField.email
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          @input="(val) => (todo.email = todo.email)"
                        />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <!-- :disabled="!flagButtonAdd" -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">No Telepon*</label>
                        <CmpInputText
                          type="number"
                          placeholder="No Telpon"
                          v-model="todo.profile_phones"
                          :class="
                            errorField.profile_phones
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          @input="onInputLimit($event, 'profile_phones', 13)"
                          :min="0"
                          :max="9999999999999"
                        />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">No Telepon 2</label>
                        <CmpInputText
                          type="number"
                          placeholder="No Telpon"
                          v-model="todo.profile_phones2"
                          :class="
                            errorField.profile_phones2
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          @input="onInputLimit($event, 'profile_phones2', 13)"
                          :min="0"
                          :max="9999999999999"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Kontak -->

          <div v-show="activeTab === 'pribadi'" class="tab-pane">
            <div class="card mt-3">
              <div class="card-header">
                <h6>Informasi Pribadi</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="example-nf-email">Nama Ayah</label>
                            <CmpInputText
                              type="text"
                              placeholder="Nama Ayah"
                              v-model="todo.nama_ayah"
                              :class="
                                errorField.nama_ayah
                                  ? 'form-control input-lg input-error'
                                  : 'form-control input-lg'
                              "
                              @input="
                                (val) =>
                                  (todo.nama_ayah =
                                    todo.nama_ayah.toUpperCase())
                              "
                            />
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="example-nf-email">Nama Ibu*</label>
                            <CmpInputText
                              id="inputA"
                              type="text"
                              placeholder="Nama Ibu"
                              v-model="todo.nama_ibu"
                              :class="
                                errorField.nama_ibu
                                  ? 'form-control input-lg input-error'
                                  : 'form-control input-lg'
                              "
                              @input="
                                (val) =>
                                  (todo.nama_ibu = todo.nama_ibu.toUpperCase())
                              "
                            />
                          </div>
                        </div>
                        <!-- :disabled="!flagButtonAdd" -->
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">Nama Anak</label>
                        <CmpInputText
                          id="inputA"
                          type="text"
                          placeholder="Nama Anak"
                          v-model="todo.nama_anak"
                          :class="
                            errorField.nama_anak
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          @input="
                            (val) =>
                              (todo.nama_anak = todo.nama_anak.toUpperCase())
                          "
                        />
                      </div>
                    </div>
                    <!-- :disabled="!flagButtonAdd" -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">Nama Pasangan</label>
                        <CmpInputText
                          type="text"
                          placeholder="Nama Pasangan"
                          v-model="todo.nama_pasangan"
                          :class="
                            errorField.nama_pasangan
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          @input="
                            (val) =>
                              (todo.nama_pasangan =
                                todo.nama_pasangan.toUpperCase())
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
                        <label for="example-nf-email">Kota Kelahiran*</label>
                        <CmpInputText
                          id="inputA"
                          type="text"
                          placeholder="Kota Kelahiran"
                          v-model="todo.birthplace"
                          :class="
                            errorField.birthplace
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          @input="
                            (val) =>
                              (todo.birthplace = todo.birthplace.toUpperCase())
                          "
                        />
                      </div>
                    </div>
                    <!-- :disabled="!flagButtonAdd" -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="example-nf-email">Tanggal Lahir*</label>
                        <CmpInputText
                          type="date"
                          placeholder="Tanggal Lahir"
                          v-model="todo.birthdate"
                          :class="
                            errorField.birthdate
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          :max="today"
                          @input="
                            (val) =>
                              (todo.birthdate = todo.birthdate.toUpperCase())
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
                        <label for="blood-type-select">Golongan Darah*</label>
                        <select
                          v-model="todo.blood_type"
                          :class="
                            errorField.blood_type
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          id="blood-type-select"
                        >
                          <option disabled value="">
                            Pilih Golongan darah
                          </option>
                          <option value="O">O</option>
                          <option value="A">A</option>
                          <option value="B">B</option>
                          <option value="AB">AB</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="religion">Agama*</label>
                        <select
                          v-model="todo.religion"
                          :class="
                            errorField.religion
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          id="religion-select"
                        >
                          <option disabled value="">Pilih Agama</option>
                          <option value="ISLAM">Islam</option>
                          <option value="KRISTEN">Kristen</option>
                          <option value="PROTESTAN">Protestan</option>
                          <option value="HINDU">Hindu</option>
                          <option value="BUDHA">Budha</option>
                          <option value="KONGHUCU">Konghucu</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="marital-status">Status Pernikahan*</label>
                        <select
                          id="marital-status"
                          v-model="todo.marital_status"
                          :class="
                            errorField.marital_status
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                        >
                          <option value="" disabled selected>
                            Pilih Status Pernikahan
                          </option>
                          <option value="BELUM MENIKAH">Belum Menikah</option>
                          <option value="MENIKAH">Menikah</option>
                          <option value="CERAI HIDUP">Cerai Hidup</option>
                          <option value="CERAI MATI">Cerai Mati</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="education-status-select"
                          >Pendidikan Terakhir*</label
                        >
                        <select
                          v-model="todo.education_status"
                          :class="
                            errorField.education_status
                              ? 'form-control input-lg input-error'
                              : 'form-control input-lg'
                          "
                          id="education-status-select"
                        >
                          <option disabled value="">
                            Pilih Pendidikan Terakhir
                          </option>
                          <option value="SD">SD</option>
                          <option value="SMP">SMP</option>
                          <option value="SMA">SMA</option>
                          <option value="D3">D3</option>
                          <option value="S1">S1</option>
                          <option value="S2">S2</option>
                          <option value="S3">S3</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-show="activeTab === 'status'" class="tab-pane">
          <div class="card-mt-3">
            <div class="card-header">
              <h6>Status Aktif</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="is_active-select">Status Aktif*</label>
                          <select
                            v-model="todo.is_active"
                            :class="
                              errorField.is_active
                                ? 'form-control input-lg input-error'
                                : 'form-control input-lg'
                            "
                            id="is_active-select"
                          >
                            <option disabled value="">Pilih Status</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-show="activeTab === 'pendukung'" class="tab-pane">
          <div class="card mt-3">
            <div class="card-header">
              <h6>Data NPWP</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Nomor NPWP</label>
                      <CmpInputText
                        type="text"
                        placeholder="Nomor NPWP"
                        v-model="todo.npwp_no"
                        :class="
                          errorField.npwp_no
                            ? 'form-control input-lg input-error'
                            : 'form-control input-lg'
                        "
                      />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Alamat NPWP</label>
                      <textarea
                        placeholder="Alamat NPWP"
                        v-model="todo.npwp_address"
                        :class="
                          errorField.npwp_address
                            ? 'form-control input-lg input-error'
                            : 'form-control input-lg'
                        "
                        rows="3"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card mt-3">
            <div class="card-header">
              <h6>Data BPJS</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Bpjs Kesehatan</label>
                      <CmpInputText
                        type="text"
                        placeholder="Bpjs Kesehatan"
                        v-model="todo.bpjs_no"
                        :class="
                          errorField.bpjs_no
                            ? 'form-control input-lg input-error'
                            : 'form-control input-lg'
                        "
                      />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Bpjs Ketenagakerjaan</label>
                      <textarea
                        placeholder="Bpjs Ketenagakerjaan"
                        v-model="todo.bpjs_ket"
                        :class="
                          errorField.bpjs_ket
                            ? 'form-control input-lg input-error'
                            : 'form-control input-lg'
                        "
                        rows="3"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card mt-3">
            <div class="card-header">
              <h6>Data Rekening</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Bank</label>
                      <CmpInputText
                        type="text"
                        placeholder="Nama Bank"
                        v-model="todo.bank_id"
                        :class="
                          errorField.bank_id
                            ? 'form-control input-lg input-error'
                            : 'form-control input-lg'
                        "
                      />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Nomor Rekening</label>
                      <CmpInputText
                        type="text"
                        placeholder="Nomor Rekening"
                        v-model="todo.karyawan_rek"
                        :class="
                          errorField.karyawan_rek
                            ? 'form-control input-lg input-error'
                            : 'form-control input-lg'
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
                      <label>KCP</label>
                      <CmpInputText
                        type="text"
                        placeholder="KCP"
                        v-model="todo.karyawan_rek_kcp"
                        :class="
                          errorField.karyawan_rek_kcp
                            ? 'form-control input-lg input-error'
                            : 'form-control input-lg'
                        "
                      />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Nama Rekening</label>
                      <CmpInputText
                        type="text"
                        placeholder="Nama Rekening"
                        v-model="todo.karyawan_name_rek"
                        :class="
                          errorField.karyawan_name_rek
                            ? 'form-control input-lg input-error'
                            : 'form-control input-lg'
                        "
                      />
                    </div>
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
                type="button"
                class="btn btn-secondary"
                @click="prevTab"
                :disabled="activeTab === 'informasi'"
              >
                Back
              </button>
              <!-- Tombol Next -->
              <button
                type="button"
                class="btn btn-primary"
                @click="nextTab"
                :disabled="activeTab === 'pendukung'"
              >
                Next
              </button>
              <button
                v-if="flagButtonAdd"
                @click="saveTodo()"
                type="button"
                class="btn btn-sm btn-primary pull-left"
                :disabled="
                  $root.flagButtonLoading ||
                  // todo.users_id == null ||
                  // todo.users_id == '' ||
                  todo.cabang_id == null ||
                  todo.cabang_id == '' ||
                  todo.jabatan_id == null ||
                  todo.jabatan_id == '' ||
                  todo.unit_id == null ||
                  todo.unit_id == '' ||
                  todo.nik == null ||
                  todo.nik == '' ||
                  todo.profile_name == null ||
                  todo.profile_name == '' ||
                  todo.profile_gender == null ||
                  todo.profile_gender == '' ||
                  todo.profile_address == null ||
                  todo.profile_address == '' ||
                  todo.postalcode == null ||
                  todo.postalcode == '' ||
                  todo.email == null ||
                  todo.email == '' ||
                  todo.profile_phones == null ||
                  todo.profile_phones == '' ||
                  todo.identity_number == null ||
                  todo.identity_number == '' ||
                  todo.birthplace == null ||
                  todo.birthplace == '' ||
                  todo.birthdate == null ||
                  todo.birthdate == '' ||
                  todo.marital_status == null ||
                  todo.marital_status == '' ||
                  todo.nama_ibu == null ||
                  todo.nama_ibu == '' ||
                  todo.blood_type == null ||
                  todo.blood_type == '' ||
                  todo.join_date == null ||
                  todo.join_date == '' ||
                  todo.religion == null ||
                  todo.religion == '' ||
                  todo.education_status == null ||
                  todo.education_status == '' ||
                  todo.is_active == null ||
                  todo.is_active == ''
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
                  false
                  // $root.flagButtonLoading ||
                  // todo.users_id == null ||
                  // todo.users_id == '' ||
                  // todo.cabang_id == null ||
                  // todo.cabang_id == '' ||
                  // todo.jabatan_id == null ||
                  // todo.jabatan_id == '' ||
                  // todo.unit_id == null ||
                  // todo.unit_id == '' ||
                  // todo.nik == null ||
                  // todo.nik == '' ||
                  // todo.profile_name == null ||
                  // todo.profile_name == '' ||
                  // todo.profile_gender == null ||
                  // todo.profile_gender == '' ||
                  // todo.profile_address == null ||
                  // todo.profile_address == '' ||
                  // todo.postalcode == null ||
                  // todo.postalcode == '' ||
                  // todo.email == null ||
                  // todo.email == '' ||
                  // todo.profile_phones == null ||
                  // todo.profile_phones == '' ||
                  // todo.identity_number == null ||
                  // todo.identity_number == '' ||
                  // todo.birthplace == null ||
                  // todo.birthplace == '' ||
                  // todo.birthdate == null ||
                  // todo.birthdate == '' ||
                  // todo.marital_status == null ||
                  // todo.marital_status == '' ||
                  // todo.nama_ibu == null ||
                  // todo.nama_ibu == '' ||
                  // todo.nama_pasangan == null ||
                  // todo.blood_type == null ||
                  // todo.blood_type == '' ||
                  // todo.join_date == null ||
                  // todo.join_date == '' ||
                  // todo.religion == null ||
                  // todo.religion == '' ||
                  // todo.education_status == null ||
                  // todo.education_status == '' ||
                  // todo.is_active == null ||
                  // todo.is_active == ''
                  // todo.npwp_no == '' ||
                  // todo.npwp_no == null ||
                  // todo.npwp_address == '' ||
                  // todo.npwp_address == null ||
                  // todo.bpjs_no == '' ||
                  // todo.bpjs_no == null ||
                  // todo.bpjs_ket == '' ||
                  // todo.bpjs_ket == null ||
                  // todo.bank_id == '' ||
                  // todo.bank_id == null ||
                  // todo.karyawan_rek == '' ||
                  // todo.karyawan_rek == null ||
                  // todo.karyawan_rek_kcp == '' ||
                  // todo.karyawan_rek_kcp == null ||
                  // todo.karyawan_name_rek == '' ||
                  // todo.karyawan_name_rek == null
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

  <!---------------------------- Modal -->

  <!-- Page content -->
  <div id="page-content" v-if="isLogin == 1" style="min-height: 850px">
    <!-- END Visible Main Sidebar Header -->

    <!-- Block -->
    <div class="block">
      <!-- Block Title -->
      <div class="block-title">
        <h2>
          <strong>Data Karyawan {{ $root.judulHalaman }}</strong>
        </h2>

        <i v-if="!status_table" class="fa fa-spinner fa-spin text-default"></i>
      </div>
      <!-- END Block Title -->

      <div class="block-content">
        <!-- Button group left side -->
        <div class="pull-left" >
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
              class="btn btn-sm btn-success"
              @click="download_excel_xyz()"
              style="margin-left: 2px;"
            >
              <i class="fas fa-file-excel"></i>
              Export Excel
            </button>
          </download-excel>

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

        <!-- Button group right side -->
        <div class="pull-right" style="margin-bottom: 15px">
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

        <!-- Clear float -->
        <div style="clear: both"></div>

        <!-- Content -->
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

import {
  VueCsvToggleHeaders,
  VueCsvSubmit,
  VueCsvMap,
  VueCsvInput,
  VueCsvErrors,
  VueCsvImport,
} from "vue-csv-import";

export default {
  components: {
    downloadExcel: JsonExcel,
    VueCsvToggleHeaders,
    VueCsvSubmit,
    VueCsvMap,
    VueCsvInput,
    VueCsvErrors,
    VueCsvImport,
    // CmpSelect2,
    // LoadingX,
    // CmpInputText,
    // CmpInputText,
  },
  data() {
    return {
      nomorBaris: 0,
      access_page: this.$root.decryptData(localStorage.getItem("halaman")),
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      activemenu: null,
      grid: new Grid(),
      // grid2: new Grid(),
      activeTab: "informasi", // Tab awal yang aktif
      errorField: {
        kode_cabang: false,
        kode_lokasi: false,
        unit_number: false,
        users_id: false,
        cabang_id: false,
        jabatan_id: false,
        unit_id: false,
        nik: false,
        profile_name: false,
        profile_gender: false,
        profile_address: false,
        postalcode: false,
        email: false,
        profile_phones: false,
        profile_phones2: false,
        identity_number: false,
        birthplace: false,
        birthdate: false,
        marital_status: false,
        nama_ayah: false,
        nama_ibu: false,
        nama_pasangan: false,
        nama_anak: false,
        blood_type: false,
        join_date: false,
        religion: false,
        education_status: false,
        is_active: false,
        npwp_no: false,
        npwp_address: false,
        bpjs_no: false,
        bpjs_ket: false,
        bank_id: false,
        karyawan_rek: false,
        karyawan_rek_kcp: false,
        karyawan_name_rek: false,
      },

      userid: 0,
      status_table: false,

      rowsPerPage: 10,
      startRow: 0,
      endRow: 0,
      totalRows: 0,

      selectedFilterCabang: null,
      filteredData: [],

      searchQuery: "",

      modal: false,
      modalCsv: false,
      csv: null,
      dataImportCsv: {
        users_id: {
          label: "users_id",
          required: true,
        },
        cabang_id: {
          label: "cabang_id",
          required: true,
        },
        kode_cabang: {
          // Tambahkan ini
          label: "kode_cabang",
          required: false,
        },
        kode_lokasi: {
          // Tambahkan ini
          label: "kode_lokasi",
          required: false,
        },
        jabatan_id: {
          label: "jabatan_id",
          required: true,
        },
        unit_id: {
          label: "unit_id",
          required: true,
        },
        nik: {
          label: "nik",
          required: true,
        },
        profile_name: {
          label: "profile_name",
          required: true,
        },
        profile_gender: {
          label: "profile_gender",
          required: true,
        },
        profile_address: {
          label: "profile_address",
          required: true,
        },
        postalcode: {
          label: "postalcode",
          required: true,
        },
        email: {
          label: "email",
          required: true,
        },
        profile_phones: {
          label: "profile_phones",
          required: true,
        },
        identity_number: {
          label: "identity_number",
          required: true,
        },
        birthplace: {
          label: "birthplace",
          required: true,
        },
        birthdate: {
          label: "birthdate",
          required: true,
        },
        marital_status: {
          label: "marital_status",
          required: true,
        },
        nama_ibu: {
          label: "nama_ibu",
          required: true,
        },
        blood_type: {
          label: "blood_type",
          required: true,
        },
        join_date: {
          label: "join_date",
          required: true,
        },
        religion: {
          label: "religion",
          required: true,
        },
        education_status: {
          label: "education_status",
          required: true,
        },
        is_active: {
          label: "is_active",
          required: true,
        },
      },

      todo: {
        users_id: "",
        cabang_id: "",
        kode_cabang: "",
        kode_lokasi: "",
        jabatan_id: "",
        unit_id: "",
        unit_number: "",
        nik: "",
        profile_name: "",
        profile_gender: "",
        profile_address: "",
        postalcode: "",
        email: "",
        profile_phones: "",
        profile_phones2: "",
        identity_number: "",
        birthplace: "",
        birthdate: "",
        marital_status: "",
        nama_ayah: "",
        nama_ibu: "",
        nama_pasangan: "",
        nama_anak: "",
        blood_type: "",
        join_date: "",
        religion: "",
        education_status: "",
        is_active: "",
        npwp_no: "",
        npwp_address: "",
        bpjs_no: "",
        bpjs_ket: "",
        bank_id: "",
        karyawan_rek: "",
        karyawan_rek_kcp: "",
        karyawan_name_rek: "",
      },

      flagButtonAdd: true,

      selectedcabang: null,
      cabangs: [],

      selectedjabatan: null,
      jabatans: [],

      selectedunit: null,
      units: [],

      selectedcabangreal: null,
      cabangreal: [],

      today: new Date().toISOString().split("T")[0], // Mengambil tanggal hari ini dalam format YYYY-MM-DD

      data_x_table: [],
      data_x_excel: [],
      nama_excelnya: "",
      nama_sheetnya: "Data Karyawan",
      json_data: [],
      json_meta: [[{ key: "charset", value: "UTF-8" }]],
      json_fields: {
        No: "nomor",
        Nama: "profile_name",
        "ID Users": "users_id",
        "Nama Cabang": "cabang_id",
        Jabatan: "jabatan_id",
        Unit: "unit_id",
        NIK: "nik",
        "Jenis Kelamin": "profile_gender",
        Alamat: "profile_address",
        "Kode POS": "postalcode",
        Email: "email",
        "Telepon 1": "profile_phones",
        "Telepon 2": "profile_phones2",
        "No Identitas": "identity_number",
        "Tempat Lahir": "birthplace",
        "Tanggal Lahir": "birthdate",
        "Status Kawin": "marital_status",
        "Nama Ayah": "nama_ayah",
        "Nama Ibu": "nama_ibu",
        "Nama Pasangan": "nama_pasangan",
        "Nama Anak": "nama_anak",
        "Golongan Darah": "blood_type",
        "Tanggal Masuk": "join_date",
        Agama: "religion",
        "Status Pendidikan": "education_status",
        "Status Aktif": "is_active",
        "NPWP NO": "npwp_no",
        "NPWP ADDRESS": "npwp_address",
        "BPJS KES": "bpjs_no",
        "BPJS KET": "bpjs_ket",
        Bank: "bank_id",
        "No. Rek": "karyawan_rek",
        KCP: "karyawan_rek_kcp",
        "Nama Rekening": "karyawan_name_rek",
      },
    };
  },
  async mounted() {
    await Promise.all([
      this.getCabang(),
      this.getCabangReal(),
      this.getJabatan(),
      this.getUnit(),
    ]);
    // await this.$root.refreshToken(localStorage.getItem("token"));
    this.getTable();
    console.log(
      "Setting userid:",
      this.$root.get_id_user(localStorage.getItem("unique"))
    );
    this.userid = this.$root.get_id_user(localStorage.getItem("unique"));
    await this.getCabang();
    await this.getCabangReal();
    await this.getJabatan();
    await this.getUnit();
  },

  watch: {
    rowsPerPage: {
      handler(newValue) {
        // Pastikan nilai rowsPerPage selalu number
        this.rowsPerPage = parseInt(newValue);
      },
      immediate: true,
    },
    todo: {
      handler(newValue, oldValue) {
        if (
          newValue.cabang_id !== oldValue.cabang_id ||
          newValue.kode_cabang !== oldValue.kode_cabang ||
          newValue.nama_cabang !== oldValue.nama_cabang ||
          newValue.unit_id !== oldValue.unit_id ||
          newValue.kode_lokasi !== oldValue.kode_lokasi ||
          newValue.jabatan_id !== oldValue.jabatan_id ||
          newValue.join_date !== oldValue.join_date
        ) {
          // Log perubahan untuk debugging
          console.log("Todo changed:", {
            cabang_id: newValue.cabang_id,
            kode_cabang: newValue.kode_cabang,
            unit_id: newValue.unit_id,
            jabatan_id: newValue.jabatan_id,
            join_date: newValue.join_date,
          });

          // Cek apakah semua data yang diperlukan tersedia
          if (
            newValue.cabang_id &&
            newValue.kode_cabang &&
            newValue.jabatan_id &&
            newValue.join_date
          ) {
            this.generateNIK();
          }
        }
      },
      deep: true,
    },
    // Tambahkan watcher untuk selected values
    selectedcabang: {
      handler(newVal) {
        if (newVal) {
          this.todo.kode_lokasi = newVal.kode_lokasi;
          if (
            this.todo.join_date &&
            this.todo.jabatan_id &&
            this.todo.cabang_id
          ) {
            this.generateNIK();
          }
        } else {
          this.todo.kode_lokasi = "";
        }
      },
      deep: true,
    },
    selectedcabangreal: {
      handler(newVal) {
        if (newVal) {
          this.todo.cabang_id = newVal.nama_cabang;
          this.todo.kode_cabang = newVal.kode_cabang;
        }
      },
      deep: true,
    },
    selectedunit: {
      handler(newVal) {
        if (newVal) {
          this.todo.unit_id = newVal.unit_name;
          this.todo.unit_number = newVal.unit_number;
        }
      },
      deep: true,
    },
    selectedjabatan: {
      handler(newVal) {
        if (newVal) {
          this.todo.jabatan_id = newVal.jabatan;
        }
      },
      deep: true,
    },
  },

  methods: {
    searchData() {
      this.grid.updateConfig({
        ...this.grid.config,
        server: {
          ...this.grid.config.server,
          url: (prev, keyword) => {
            // Periksa apakah selectedFilterCabang ada nilainya
            if (this.selectedFilterCabang) {
              return `${prev}?search=${
                this.searchQuery
              }&cabang_id=${encodeURIComponent(
                this.selectedFilterCabang.nama_cabang
              )}`;
            } else {
              return `${prev}?search=${this.searchQuery}`;
            }
          },
        },
      });
      this.grid.forceRender();
    },
    filterByCabang(selected) {
      this.selectedFilterCabang = selected;
      this.searchData(); // Panggil searchData() setelah memperbarui selectedFilterCabang
    },

    async filterByCabang(selected) {
      try {
        if (selected) {
          // Tambahkan console.log untuk debugging
          console.log("Selected cabang:", selected);

          // Update grid config dengan URL yang benar
          const baseUrl = `${this.$root.apiHost}api/profiles`;
          const url = `${baseUrl}?cabang_id=${encodeURIComponent(
            selected.nama_cabang
          )}`;

          // Update grid config dengan URL baru
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
          // Reset ke URL original
          this.grid.updateConfig({
            ...this.grid.config,
            server: {
              ...this.grid.config.server,
              url: `${this.$root.apiHost}api/profiles`,
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

    toggleModalCsv() {
      this.modalCsv = !this.modalCsv;
      if (!this.modalCsv) {
        this.csv = null;
      }
    },

    async saveTodoBulky() {
      try {
        const result = await Swal.fire({
          title: "Create Profiles Data",
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
            this.$root.apiHost + "api/profiles/storeBulky",
            {
              csv: this.csv,
              userid: this.userid,
              todo: this.todo,
            }
          );

          await Swal.fire(
            "Created!",
            response.data.message || "Data has been created successfully",
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
            toast.error(error.response.data.message, {
              theme: "colored",
            });
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
    async saveRekening(profileId) {
      if (this.todo.karyawan_rek && this.todo.karyawan_rek_kcp) {
        try {
          const response = await axios.post(
            `${this.$root.apiHost}api/hr_rek_karyawan`,
            {
              profiles_id: profileId,
              karyawan_rek: this.todo.karyawan_rek,
              karyawan_rek_kcp: this.todo.karyawan_rek_kcp,
              created_by: this.userid,
              userid: this.userid,
            }
          );
          console.log("Rekening saved:", response.data);
        } catch (error) {
          console.error("Error saving rekening:", error);
          toast.error("Gagal menyimpan data rekening", { theme: "colored" });
        }
      }
    },
    resetTodo() {
      this.todo = {
        users_id: "",
        cabang_id: "",
        kode_cabang: "",
        kode_lokasi: "",
        jabatan_id: "",
        unit_id: "",
        unit_number: "",
        nik: "",
        profile_name: "",
        profile_gender: "",
        profile_address: "",
        postalcode: "",
        email: "",
        profile_phones: "",
        profile_phones2: "",
        identity_number: "",
        birthplace: "",
        birthdate: "",
        marital_status: "",
        nama_ayah: "",
        nama_ibu: "",
        nama_pasangan: "",
        nama_anak: "",
        blood_type: "",
        join_date: "",
        religion: "",
        education_status: "",
        is_active: "",
      };

      // Reset juga nilai yang terhubung dengan v-select
      this.selectedcabangreal = null;
      this.selectedcabang = null;
      this.selectedjabatan = null;
      this.selectedunit = null;
    },

    setActiveTab(tabName) {
      this.activeTab = tabName; // Mengubah tab yang aktif
    },
    prevTab() {
      const tabs = ["informasi", "kontak", "pribadi", "status", "pendukung"];
      const currentIndex = tabs.indexOf(this.activeTab);
      if (currentIndex > 0) {
        this.setActiveTab(tabs[currentIndex - 1]);
      }
    },
    nextTab() {
      // Daftar tab yang ada
      const tabs = ["informasi", "kontak", "pribadi", "status", "pendukung"];
      // Mendapatkan indeks tab saat ini
      const currentIndex = tabs.indexOf(this.activeTab);
      // Jika masih ada tab berikutnya, ganti ke tab tersebut
      if (currentIndex < tabs.length - 1) {
        this.setActiveTab(tabs[currentIndex + 1]);
      }
    },
    async generateNIK() {
      try {
        const {
          kode_lokasi,
          jabatan_id,
          unit_number,
          cabang_id,
          kode_cabang,
          join_date,
          nik, // tambahkan ini untuk mengecek jika sedang update
        } = this.todo;

        // Log data untuk debugging
        console.log("Data for NIK generation:", {
          kode_lokasi,
          jabatan_id,
          unit_number,
          cabang_id,
          kode_cabang,
          join_date,
          existing_nik: nik,
        });

        if (!join_date || !cabang_id || !kode_cabang) {
          console.error("Missing required data for NIK generation");
          return;
        }

        const yearMonth = this.formatDateForNIK(new Date(join_date));

        // Jika sudah ada NIK (update mode), ambil 2 digit terakhir
        let urutan;
        if (nik) {
          urutan = nik.slice(-2); // Ambil 2 digit terakhir dari NIK yang ada
          console.log("Using existing urutan:", urutan);
        } else {
          // Generate urutan baru hanya jika ini adalah NIK baru
          urutan = await this.generateUrutan(cabang_id);
          console.log("Generated new urutan:", urutan);
        }

        // Gunakan kode_cabang untuk NIK
        try {
          if (
            [
              "ACCOUNT SUPERVISOR",
              "ACCOUNT AREA MANAGER",
              "FINANCE STAFF",
              "SALESMAN",
            ].includes(jabatan_id)
          ) {
            this.todo.nik = `${unit_number}${kode_lokasi}${yearMonth}${kode_cabang}${urutan}`;
            console.log("Management NIK:", this.todo.nik);
          } else if (
            ["BEAUTY CONSULTANT", "BEAUTY ADVISOR", "MERCHANDISER"].includes(
              jabatan_id
            )
          ) {
            this.todo.nik = `12${kode_lokasi}${yearMonth}${kode_cabang}${urutan}`;
            console.log("BA/BC NIK:", this.todo.nik);
          } else if (["DRIVER", "PICKER", "CHECKER"].includes(jabatan_id)) {
            this.todo.nik = `14${kode_lokasi}${yearMonth}${kode_cabang}${urutan}`;
            console.log("Logistics NIK:", this.todo.nik);
          } else {
            this.todo.nik = `${unit_number}${kode_lokasi}${yearMonth}${kode_cabang}${urutan}`;
            console.log("Other NIK:", this.todo.nik);
          }
        } catch (error) {
          console.error("Error generating NIK:", error);
          this.todo.nik = "";
        }
      } catch (error) {
        console.error("Error in generateNIK:", error);
      }
    },
    formatDateForNIK(dateString) {
      // Konversi string ke Date object
      const date = new Date(dateString);

      // Cek apakah date valid
      if (isNaN(date.getTime())) {
        console.error("Invalid date for NIK:", dateString);
        return "";
      }

      const year = date.getFullYear().toString().slice(-2);
      const month = this.padTo2Digits(date.getMonth() + 1);
      return `${year}${month}`;
    },

    padTo2Digits(num) {
      return num.toString().padStart(2, "0");
    },

    // Fungsi helper untuk memvalidasi format tanggal
    isValidDate(dateString) {
      const date = new Date(dateString);
      return !isNaN(date.getTime());
    },

    // Fungsi untuk mendapatkan urutan NIK berdasarkan cabang_id
    async generateUrutan(cabang_id) {
      try {
        // Tetap menggunakan cabang_id untuk mencari urutan terakhir
        const response = await axios.get(
          `${this.$root.apiHost}api/lastNIK/${cabang_id}`
        );

        console.log("Last NIK Response:", response);

        if (response.data.nik) {
          const lastNIK = response.data.nik;
          const lastUrutan = parseInt(lastNIK.slice(-2)); // Ambil 2 digit terakhir
          const nextUrutan = lastUrutan + 1;
          // Return hanya urutan tanpa prefix
          return nextUrutan.toString().padStart(2, "0");
        } else {
          return "01"; // Return urutan awal tanpa prefix
        }
      } catch (error) {
        console.error("Error fetching last NIK:", error);
        return "01";
      }
    },

    padTo2Digits(num) {
      return num.toString().padStart(2, "0");
    },
    formatDate(dateString) {
      // Konversi string ke Date object
      const date = new Date(dateString);

      // Cek apakah date valid
      if (isNaN(date.getTime())) {
        console.error("Invalid date:", dateString);
        return "";
      }

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
      mythis.data_x_excel = [];

      while (count > 0) {
        offsetx = limitx * nn;

        const reqData = await axios({
          method: "get",
          url:
            mythis.$root.apiHost +
            "api/profiles?offset=" +
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
            users_id: resData.results[key].users_id,
            cabang_id: resData.results[key].cabang_id,
            jabatan_id: resData.results[key].jabatan_id,
            unit_id: resData.results[key].unit_id,
            nik: resData.results[key].nik,
            profile_name: resData.results[key].profile_name,
            profile_gender: resData.results[key].profile_gender,
            profile_address: resData.results[key].profile_address,
            postalcode: resData.results[key].postalcode,
            email: resData.results[key].email,
            profile_phones: resData.results[key].profile_phones,
            profile_phones2: resData.results[key].profile_phones2,
            identity_number: resData.results[key].identity_number,
            birthplace: resData.results[key].birthplace,
            birthdate: resData.results[key].birthdate,
            marital_status: resData.results[key].marital_status,
            nama_ayah: resData.results[key].nama_ayah,
            nama_ibu: resData.results[key].nama_ibu,
            nama_pasangan: resData.results[key].nama_pasangan,
            nama_anak: resData.results[key].nama_anak,
            blood_type: resData.results[key].blood_type,
            join_date: resData.results[key].join_date,
            religion: resData.results[key].religion,
            education_status: resData.results[key].education_status,
            is_active: resData.results[key].is_active,
            npwp_no: resData.results[key].npwp_no || "-",
            npwp_address: resData.results[key].npwp_address || "-",
            bpjs_no: resData.results[key].bpjs_no || "-",
            bpjs_ket: resData.results[key].bpjs_ket || "-",
            bank_id: resData.results[key].bank_id || "-",
            karyawan_rek: resData.results[key].karyawan_rek || "-",
            karyawan_rek_kcp: resData.results[key].karyawan_rek_kcp || "-",
            karyawan_name_rek: resData.results[key].karyawan_name_rek || "-",
          };
          mythis.data_x_excel[baris_excel] = countries_x;

          br_pdf++;
          baris_excel++;
          nomor_x++;
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
        nomor: "Print Date",
        users_id: "",
        cabang_id: "",
        jabatan_id: "",
        unit_id: "",
        nik: "",
        profile_name: mythis.formatDate(new Date()),
        profile_gender: "",
        profile_address: "",
        postalcode: "",
        email: "",
        profile_phones: "",
        profile_phones2: "",
        identity_number: "",
        birthplace: "",
        birthdate: "",
        marital_status: "",
        nama_ayah: "",
        nama_ibu: "",
        nama_pasangan: "",
        nama_anak: "",
        blood_type: "",
        join_date: "",
        religion: "",
        education_status: "",
        is_active: "",
        npwp_no: "",
        npwp_address: "",
        bpjs_no: "",
        bpjs_ket: "",
        bank_id: "",
        karyawan_rek: "",
        karyawan_rek_kcp: "",
        karyawan_name_rek: "",
      };
      mythis.data_x_excel[baris_excel] = countries_x;

      mythis.json_data = mythis.data_x_excel;
      mythis.flagDownloadXLSX = 1;

      var a = new Date().toLocaleString("en-GB");
      mythis.nama_excelnya = "Data_Karyawan_" + a + ".xlsx";
      mythis.nama_sheetnya = "Data Karyawan";

      mythis.$root.stopLoading();
    },

    download_excel_xyz() {},
    async startDownload() {
      await this.getDataExportExcel();
    },
    finishDownload() {
      this.nama_excelnya = "";
      this.nama_sheetnya = "";
    },

    onChangeJoinDate() {
      // Jika cabang sudah dipilih, generate NIK
      if (this.todo.cabang_id && this.todo.join_date) {
        this.generateNIK();
      }
    },

    onChangeCabangReal(selected) {
      if (selected) {
        // Update todo.cabang_id dengan nama_cabang
        this.todo.cabang_id = selected.nama_cabang;
        // Simpan kode_cabang untuk generate NIK
        this.todo.kode_cabang = selected.kode_cabang;
        this.todo.nik = "";

        // Generate NIK jika semua data yang diperlukan sudah ada
        if (this.todo.join_date) {
          this.generateNIK();
        }
      } else {
        this.todo.cabang_id = "";
        this.todo.kode_cabang = "";
        this.todo.nik = "";
      }
    },

    async getCabangReal() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/cabangalldata"
        );
        // Pastikan response mengandung nama_cabang dan kode_cabang
        this.cabangreal = response.data.map((cabang) => ({
          nama_cabang: cabang.nama_cabang,
          kode_cabang: cabang.kode_cabang,
          // property lain yang diperlukan
        }));
      } catch (error) {
        console.error("Error fetching cabang:", error);
      }
    },

    onChangeCabang(selectedcabang) {
      if (selectedcabang) {
        this.selectedcabang = selectedcabang;
        this.todo.kode_lokasi = selectedcabang.kode_lokasi;

        if (
          this.todo.join_date &&
          this.todo.jabatan_id &&
          this.todo.cabang_id
        ) {
          this.generateNIK();
        }
      } else {
        this.todo.kode_lokasi = "";
        this.selectedcabang = null;
        this.todo.nik = "";
      }
    },
    async getCabang() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/kodelokasialldata"
        );
        this.cabangs = response.data;

        // Pastikan cabangs ada sebelum mengakses indeks pertama
        if (this.cabangs.length > 0) {
          this.selectedcabang = this.cabangs[0]; // Pilih cabang pertama secara default
        } else {
          this.selectedcabang = null; // Tidak ada data cabang
        }
      } catch (error) {
        console.error("Error fetching cabangs:", error);
      }
    },

    onChangeJabatan(selectedjabatan) {
      if (selectedjabatan) {
        this.todo.jabatan_id = selectedjabatan.jabatan;
        this.selectedjabatan = selectedjabatan;
        // Tetap pertahankan NIK jika sudah ada
        if (this.todo.join_date && this.todo.cabang_id) {
          this.generateNIK(); // Panggil hanya jika NIK belum ada
        }
      } else {
        this.todo.jabatan_id = "";
        this.selectedjabatan = null;
        this.todo.nik = ""; // Kosongkan NIK jika tidak ada jabatan yang dipilih
      }
    },

    async getJabatan() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/jabatanalldata"
        );
        this.jabatans = [
          { jabatan: "" }, // Tambah opsi kosong
          ...response.data,
        ];
        this.selectedjabatan = this.jabatans[0]; // Pilih opsi kosong
      } catch (error) {
        console.error("Error fetching jabatans:", error);
      }
    },
    onChangeUnit(selected) {
      if (selected) {
        console.log("Selected unit:", selected); // Debug selected data

        // Menggunakan unit_name untuk disimpan ke database
        this.todo.unit_id = selected.unit_name;
        // Menyimpan unit_number untuk keperluan generate NIK
        this.todo.unit_number = selected.unit_number;

        // Generate NIK jika semua data yang diperlukan sudah ada
        this.todo.nik = "";
        this.generateNIK();
      } else {
        this.todo.unit_id = "";
        this.todo.unit_number = "";
        this.todo.nik = "";
      }
    },
    async getUnit() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/unitalldata"
        );
        // Map response data untuk memastikan struktur yang benar
        this.units = response.data.map((unit) => ({
          id: unit.id,
          unit_name: unit.unit_name,
          unit_number: unit.unit_number,
          // tambahkan properti lain yang diperlukan
        }));

        console.log("Units loaded:", this.units); // Debug loaded units
      } catch (error) {
        console.error("Error fetching units:", error);
        toast.error("Gagal mengambil data unit", { theme: "colored" });
      }
    },

    onInput(event) {
      let value = event.target.value;

      // Membatasi input menjadi maksimal 5 digit
      if (value.length > 5) {
        value = value.slice(0, 5); // Potong input hingga 5 digit
      }

      // Set nilai ke model dan input elemen
      this.todo.postalcode = value;
      event.target.value = value; // Memastikan tampilan diperbarui
    },

    onNIKInput(event, field, maxLength) {
      let value = event.target.value;

      // Membatasi input menjadi maksimal 16 digit
      if (value.length > 16) {
        value = value.slice(0, 16); // Potong input hingga 16 digit
      }

      // Set nilai ke model dan input elemen
      this.todo[field] = value;
      event.target.value = value; // Memastikan tampilan diperbarui
    },

    onInputLimit(event, field, maxLength) {
      let value = event.target.value;

      // Membatasi input sesuai maxLength
      if (value.length > maxLength) {
        value = value.slice(0, maxLength); // Potong input sesuai batasan
      }

      // Set nilai ke model yang sesuai
      this.todo[field] = value;
      event.target.value = value; // Memastikan tampilan diperbarui
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
        title: "Create Data Karyawan",
        text: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        cancelButtonText: "Cancel",
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            mythis.$root.presentLoading();
            mythis.$root.flagButtonLoading = true;
            const config = "";

            // Save main profile first
            const profileResponse = await axios.post(
              mythis.$root.apiHost + "api/profiles",
              {
                // users_id: mythis.todo.users_id,
                cabang_id: mythis.todo.cabang_id,
                kode_lokasi: mythis.todo.kode_lokasi,
                jabatan_id: mythis.todo.jabatan_id,
                unit_id: mythis.todo.unit_id,
                nik: mythis.todo.nik,
                profile_name: mythis.todo.profile_name,
                profile_gender: mythis.todo.profile_gender,
                profile_address: mythis.todo.profile_address,
                postalcode: mythis.todo.postalcode,
                email: mythis.todo.email,
                profile_phones: mythis.todo.profile_phones,
                profile_phones2: mythis.todo.profile_phones2,
                identity_number: mythis.todo.identity_number,
                birthplace: mythis.todo.birthplace,
                birthdate: mythis.todo.birthdate,
                marital_status: mythis.todo.marital_status,
                nama_ayah: mythis.todo.nama_ayah,
                nama_ibu: mythis.todo.nama_ibu,
                nama_pasangan: mythis.todo.nama_pasangan,
                nama_anak: mythis.todo.nama_anak,
                blood_type: mythis.todo.blood_type,
                join_date: mythis.todo.join_date,
                religion: mythis.todo.religion,
                education_status: mythis.todo.education_status,
                is_active: mythis.todo.is_active,
                userid: mythis.userid,
              },
              config
            );

            const profiles_id = mythis.todo.profile_name;

            // Save NPWP data if exists
            if (mythis.todo.npwp_no || mythis.todo.npwp_address) {
              await axios.post(
                `${mythis.$root.apiHost}api/hr_npwp`,
                {
                  profiles_id: profiles_id,
                  npwp_no: mythis.todo.npwp_no,
                  npwp_address: mythis.todo.npwp_address,
                  created_by: mythis.userid,
                  userid: mythis.userid,
                },
                config
              );
            }

            // Save BPJS data if exists
            if (mythis.todo.bpjs_no || mythis.todo.bpjs_ket) {
              await axios.post(
                `${mythis.$root.apiHost}api/hr_bpjs`,
                {
                  profiles_id: profiles_id,
                  bpjs_no: mythis.todo.bpjs_no,
                  bpjs_ket: mythis.todo.bpjs_ket,
                  created_by: mythis.userid,
                  userid: mythis.userid,
                },
                config
              );
            }

            // Save bank account data if exists
            if (mythis.todo.karyawan_rek || mythis.todo.karyawan_rek_kcp) {
              await axios.post(
                `${mythis.$root.apiHost}api/hr_rek_karyawan`,
                {
                  profiles_id: profiles_id,
                  bank_id: mythis.todo.bank_id,
                  karyawan_rek: mythis.todo.karyawan_rek,
                  karyawan_rek_kcp: mythis.todo.karyawan_rek_kcp,
                  karyawan_name_rek: mythis.todo.karyawan_name_rek,
                  created_by: mythis.userid,
                  userid: mythis.userid,
                },
                config
              );
            }

            Swal.fire("Created!", "Data berhasil disimpan", "success");
            mythis.$root.stopLoading();
            mythis.$root.flagButtonLoading = false;
            mythis.resetForm();
            mythis.show_modal();
            mythis.refreshTable();
          } catch (error) {
            mythis.$root.flagButtonLoading = false;
            mythis.$root.stopLoading();

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
          }
        }
      });
    },

    show_modal(isAddMode = true) {
      this.modal = !this.modal;

      if (this.modal) {
        // Jika modal dibuka
        if (isAddMode) {
          // Jika dalam mode tambah, reset todo
          this.flagButtonAdd = true;
          this.resetTodo();
        } else {
          // Jika dalam mode update, tetap pertahankan data yang ada
          this.flagButtonAdd = false;
        }
      } else {
        // Jika modal ditutup
        this.resetForm();
        this.flagButtonAdd = true;
      }
    },

    async jqueryDelEdit() {
      const mythis = this;
      $(document).on("click", "#editData", async function () {
        let id = $(this).data("id");
        await mythis.getData(id);
        mythis.show_modal(false);
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
          limit: parseInt(mythis.rowsPerPage),
          server: {
            url: (prev, page, limit) => {
              // Periksa apakah selectedFilterCabang ada nilainya
              if (this.selectedFilterCabang) {
                return `${prev}${
                  prev.includes("?") ? "&" : "?"
                }limit=${limit}&offset=${page * limit}&search=${
                  this.searchQuery
                }&cabang_id=${encodeURIComponent(
                  this.selectedFilterCabang.nama_cabang
                )}`;
              } else {
                return `${prev}${
                  prev.includes("?") ? "&" : "?"
                }limit=${limit}&offset=${page * limit}&search=${
                  this.searchQuery
                }`;
              }
            },
          },
        },
        search: {
          input: "#search-input",
          operator: "contains",
        },
        columns: [
          { name: "ID", hidden: true },
          "No",
          "Nama",
          // "ID Users",
          "Nama Cabang",
          "Jabatan",
          "Unit",
          "NIK",
          "Jenis Kelamin",
          "Alamat",
          // "Kode POS",
          // "Email",
          "Telepon 1",
          // "Telepon 2",
          // "No Identitas",
          "Tempat Lahir",
          "Tanggal Lahir",
          // "Status Kawin",
          // "Nama Ayah",
          // "Nama Ibu",
          // "Nama Pasangan",
          // "Nama Anak",
          // "Golongan Darah",
          "Tanggal Masuk",
          // "Agama",
          // "Status Pendidikan",
          "Status Aktif",
          // "NPWP NO",
          // "NPWP ADDRESS",
          // "BPJS KES",
          // "BPJS KET",
          // "Bank",
          // "No. Rek",
          // "KCP",
          // "Nama Rekening",

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
          url: this.$root.apiHost + "api/profiles",
          then: (data) => {
            // Parse data sebagai number untuk menghindari NaN
            const offset = parseInt(data.offset || 0);
            const total = parseInt(data.count || 0);
            const limit = parseInt(this.rowsPerPage);

            // Update pagination info
            this.totalRows = total;
            this.startRow = offset + 1;
            this.endRow = Math.min(offset + limit, total);

            // Tampilkan 0 jika tidak ada data
            if (total === 0) {
              this.startRow = 0;
              this.endRow = 0;
            }
            return data.results.map((card, index) => [
              card.id,
              (data.offset || 0) + index + 1,
              html(`<span class="pull-left">${card.profile_name}</span>`),
              // html(`<span class="pull-left">${card.users_id}</span>`),
              html(`<span class="pull-left">${card.cabang_id}</span>`),
              html(`<span class="pull-left">${card.jabatan_id}</span>`),
              html(`<span class="pull-left">${card.unit_id}</span>`),
              html(`<span class="pull-left">${card.nik}</span>`),
              html(`<span class="pull-left">${card.profile_gender}</span>`),
              html(`<span class="pull-left">${card.profile_address}</span>`),
              // html(`<span class="pull-left">${card.postalcode}</span>`),
              // html(`<span class="pull-left">${card.email}</span>`),
              html(`<span class="pull-left">${card.profile_phones}</span>`),
              // html(`<span class="pull-left">${card.profile_phones2}</span>`),
              // html(`<span class="pull-left">${card.identity_number}</span>`),
              html(`<span class="pull-left">${card.birthplace}</span>`),
              html(`<span class="pull-left">${card.birthdate}</span>`),
              // html(`<span class="pull-left">${card.marital_status}</span>`),
              // html(`<span class="pull-left">${card.nama_ayah}</span>`),
              // html(`<span class="pull-left">${card.nama_ibu}</span>`),
              // html(`<span class="pull-left">${card.nama_pasangan}</span>`),
              // html(`<span class="pull-left">${card.nama_anak}</span>`),
              // html(`<span class="pull-left">${card.blood_type}</span>`),
              html(`<span class="pull-left">${card.join_date}</span>`),
              // html(`<span class="pull-left">${card.religion}</span>`),
              // html(`<span class="pull-left">${card.education_status}</span>`),
              html(`<span class="pull-left">${card.is_active}</span>`),
              // html(`<span class="pull-left">${card.npwp_no || "-"}</span>`),
              // html(
              //   `<span class="pull-left">${card.npwp_address || "-"}</span>`
              // ),
              // html(`<span class="pull-left">${card.bpjs_no || "-"}</span>`),
              // html(`<span class="pull-left">${card.bpjs_ket || "-"}</span>`),
              // html(`<span class="pull-left">${card.bank_id || "-"}</span>`),
              // html(
              //   `<span class="pull-left">${card.karyawan_rek || "-"}</span>`
              // ),
              // html(
              //   `<span class="pull-left">${card.karyawan_rek_kcp || "-"}</span>`
              // ),
              // html(
              //   `<span class="pull-left">${
              //     card.karyawan_name_rek || "-"
              //   }</span>`
              // ),
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
            .delete(mythis.$root.apiHost + `api/profiles/${id}`, config)
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

    async getData(id) {
      var mythis = this;
      mythis.flagButtonAdd = false;
      mythis.$root.presentLoading();

      try {
        // Ambil data profile dan data tambahan secara parallel
        const [profileRes, npwpRes, bpjsRes, rekeningRes] = await Promise.all([
          axios.get(mythis.$root.apiHost + `api/profiles/${id}`),
          axios.get(mythis.$root.apiHost + `api/npwp/profile/${id}`), // Gunakan endpoint getByProfileId
          axios.get(mythis.$root.apiHost + `api/bpjs/profile/${id}`), // Gunakan endpoint getByProfileId
          axios.get(mythis.$root.apiHost + `api/rekkar/profile/${id}`), // Gunakan endpoint getByProfileId
        ]);

        // Set data profile seperti sebelumnya
        mythis.todo = {
          id: id,
          users_id: profileRes.data.data.users_id,
          nik: profileRes.data.data.nik,
          cabang_id: profileRes.data.data.cabang_id,
          kode_lokasi: profileRes.data.data.kode_lokasi,
          jabatan_id: profileRes.data.data.jabatan_id,
          unit_id: profileRes.data.data.unit_id,
          identity_number: profileRes.data.data.identity_number,
          birthplace: profileRes.data.data.birthplace,
          birthdate: profileRes.data.data.birthdate,
          profile_name: profileRes.data.data.profile_name,
          profile_address: profileRes.data.data.profile_address,
          postalcode: profileRes.data.data.postalcode,
          profile_gender: profileRes.data.data.profile_gender,
          blood_type: profileRes.data.data.blood_type,
          religion: profileRes.data.data.religion,
          marital_status: profileRes.data.data.marital_status,
          nama_ayah: profileRes.data.data.nama_ayah,
          nama_ibu: profileRes.data.data.nama_ibu,
          nama_pasangan: profileRes.data.data.nama_pasangan,
          nama_anak: profileRes.data.data.nama_anak,
          email: profileRes.data.data.email,
          profile_phones: profileRes.data.data.profile_phones,
          profile_phones2: profileRes.data.data.profile_phones2,
          join_date: profileRes.data.data.join_date,
          education_status: profileRes.data.data.education_status,
          is_active: profileRes.data.data.is_active,

          // Set data NPWP jika ada
          npwp_no: npwpRes.data?.data?.npwp_no || "",
          npwp_address: npwpRes.data?.data?.npwp_address || "",

          // Set data BPJS jika ada
          bpjs_no: bpjsRes.data?.data?.bpjs_no || "",
          bpjs_ket: bpjsRes.data?.data?.bpjs_ket || "",

          // Set data rekening jika ada
          bank_id: rekeningRes.data?.data?.bank_id || "",
          karyawan_rek: rekeningRes.data?.data?.karyawan_rek || "",
          karyawan_rek_kcp: rekeningRes.data?.data?.karyawan_rek_kcp || "",
          karyawan_name_rek: rekeningRes.data?.data?.karyawan_name_rek || "",
        };

        // Set selected values untuk v-select components seperti sebelumnya
        if (mythis.cabangreal?.length) {
          mythis.selectedcabangreal = mythis.cabangreal.find(
            (c) => c.nama_cabang === profileRes.data.data.cabang_id
          );
          if (mythis.selectedcabangreal) {
            mythis.todo.kode_cabang = mythis.selectedcabangreal.kode_cabang;
          }
        }

        if (mythis.cabangs?.length) {
          mythis.selectedcabang = mythis.cabangs.find(
            (cab) => cab.kode_lokasi === profileRes.data.data.kode_lokasi
          );
          if (mythis.selectedcabang) {
            mythis.todo.kode_lokasi = mythis.selectedcabang.kode_lokasi;
          }
        }

        if (mythis.units?.length) {
          mythis.selectedunit = mythis.units.find(
            (u) => u.unit_name === profileRes.data.data.unit_id
          );
          if (mythis.selectedunit) {
            mythis.todo.unit_number = mythis.selectedunit.unit_number;
          }
        }

        if (mythis.jabatans?.length) {
          mythis.selectedjabatan = mythis.jabatans.find(
            (jabatan) => jabatan.jabatan === mythis.todo.jabatan_id
          );
        }

        mythis.$root.stopLoading();
      } catch (error) {
        console.error("Error in getData:", error);
        toast.error("Gagal mengambil data karyawan", { theme: "colored" });
        mythis.$root.stopLoading();
      }
    },

    editTodo() {
      var mythis = this;
      mythis.$root.flagButtonLoading = true;
      const config = {};

      // Main profile update
      axios
        .put(
          mythis.$root.apiHost + "api/profiles/" + mythis.todo.id,
          {
            users_id: mythis.todo.users_id,
            cabang_id: mythis.todo.cabang_id,
            cabang_id: mythis.todo.cabang_id,
            kode_cabang: mythis.todo.kode_cabang || "",
            kode_lokasi: mythis.todo.kode_lokasi || "",
            jabatan_id: mythis.todo.jabatan_id,
            unit_id: mythis.todo.unit_id,
            nik: mythis.todo.nik || "",
            profile_name: mythis.todo.profile_name,
            profile_gender: mythis.todo.profile_gender,
            profile_address: mythis.todo.profile_address,
            postalcode: mythis.todo.postalcode,
            email: mythis.todo.email,
            profile_phones: mythis.todo.profile_phones,
            profile_phones2: mythis.todo.profile_phones2,
            identity_number: mythis.todo.identity_number,
            birthplace: mythis.todo.birthplace,
            birthdate: mythis.todo.birthdate,
            marital_status: mythis.todo.marital_status,
            nama_ayah: mythis.todo.nama_ayah,
            nama_ibu: mythis.todo.nama_ibu,
            nama_pasangan: mythis.todo.nama_pasangan,
            nama_anak: mythis.todo.nama_anak,
            blood_type: mythis.todo.blood_type,
            join_date: mythis.todo.join_date,
            religion: mythis.todo.religion,
            education_status: mythis.todo.education_status,
            is_active: mythis.todo.is_active,
            userid: mythis.userid,
          },
          config
        )
        .then((res) => {
          const updatePromises = [];

          // Helper function untuk menyimpan atau memperbarui data
          const createOrUpdate = async (type, data) => {
            try {
              // Coba mendapatkan data yang ada
              const checkResponse = await axios.get(
                `${mythis.$root.apiHost}api/${type}/${mythis.todo.id}`
              );

              if (checkResponse.data && checkResponse.data.data) {
                // Data ada, lakukan update
                return axios.put(
                  `${mythis.$root.apiHost}api/${type}/${mythis.todo.id}`,
                  {
                    ...data,
                    updated_by: mythis.userid,
                  }
                );
              }
            } catch (error) {
              if (error.response && error.response.status === 404) {
                // Data tidak ditemukan, buat baru
                return axios.post(`${mythis.$root.apiHost}api/${type}`, {
                  ...data,
                  created_by: mythis.userid,
                  updated_by: mythis.userid,
                });
              }
              console.error(`Error handling ${type}:`, error);
            }
          };

          // Update NPWP if any field is filled
          if (mythis.todo.npwp_no || mythis.todo.npwp_address) {
            updatePromises.push(
              createOrUpdate("hr_npwp", {
                profiles_id: mythis.todo.id,
                npwp_no: mythis.todo.npwp_no || "",
                npwp_address: mythis.todo.npwp_address || "",
                userid: mythis.userid,
              })
            );
          }

          // Update BPJS if any field is filled
          if (mythis.todo.bpjs_no || mythis.todo.bpjs_ket) {
            updatePromises.push(
              createOrUpdate("hr_bpjs", {
                profiles_id: mythis.todo.id,
                bpjs_no: mythis.todo.bpjs_no || "",
                bpjs_ket: mythis.todo.bpjs_ket || "",
                userid: mythis.userid,
              })
            );
          }

          // Update Rekening if any field is filled
          if (
            mythis.todo.bank_id ||
            mythis.todo.karyawan_rek ||
            mythis.todo.karyawan_rek_kcp ||
            mythis.todo.karyawan_name_rek
          ) {
            updatePromises.push(
              createOrUpdate("hr_rek_karyawan", {
                profiles_id: mythis.todo.id,
                bank_id: mythis.todo.bank_id || "",
                karyawan_rek: mythis.todo.karyawan_rek || "",
                karyawan_rek_kcp: mythis.todo.karyawan_rek_kcp || "",
                karyawan_name_rek: mythis.todo.karyawan_name_rek || "",
                userid: mythis.userid,
              })
            );
          }

          Promise.allSettled(updatePromises)
            .then((results) => {
              // Log results for debugging
              results.forEach((result, index) => {
                if (result.status === "rejected") {
                  console.error(`Failed promise ${index}:`, result.reason);
                }
              });

              // Continue with success flow regardless of dependent updates
              Swal.fire("Updated!", res.data.message, "success");
              mythis.$root.flagButtonLoading = false;
              mythis.resetForm();
              mythis.show_modal();

              if (mythis.grid) {
                mythis.grid.destroy();
              }
              mythis.getTable();

              mythis.$nextTick(() => {
                mythis.status_table = true;
              });
            })
            .catch((err) => {
              console.error("Error in updates:", err);
              // Still show success since main profile was updated
              Swal.fire("Updated!", res.data.message, "success");
              mythis.$root.flagButtonLoading = false;
              mythis.resetForm();
              mythis.show_modal();
              mythis.getTable();
            });
        })
        .catch(function (error) {
          mythis.$root.flagButtonLoading = false;
          if (error.response) {
            if (error.response.status == 422) {
              mythis.errorList = error.response.data;
              mythis.$root.loader = false;
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
}
</style>
