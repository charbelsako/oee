<div class="modal fade" id="kt_modal_add_device" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_device_header">
                <h2 class="fw-bold">Add Device</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary"
                     data-kt-devices-modal-action="close" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                  transform="rotate(-45 6 17.3137)" fill="currentColor"/>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                  transform="rotate(45 7.41422 6)" fill="currentColor"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_device_form" class="form"
                      data-action="{{ route('devices.store') }}">
                    @csrf
                    <div class="d-flex flex-column scroll-y me-n7 pe-7"
                         id="kt_modal_add_device_scroll" data-kt-scroll="true"
                         data-kt-scroll-activate="{default: false, lg: true}"
                         data-kt-scroll-max-height="auto"
                         data-kt-scroll-dependencies="#kt_modal_add_device_header"
                         data-kt-scroll-wrappers="#kt_modal_add_device_scroll"
                         data-kt-scroll-offset="300px">
                        <div class="fv-row mb-7" id="device_temp_div">
                            <label class="required fw-semibold fs-6 mb-2">Temp Device</label>
                            <select class="form-control form-control-solid mb-3 mb-lg-0"
                                    name="device_temp_id" id="device_temp_id">
                                <option>please choose temp device</option>
                                @foreach($temps as $temp)
                                    <option value="{{ $temp->id }}">{{ $temp->mac_address . ' - ' . $temp->prefix }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Project</label>
                            <input type="text" name="project" id="project"
                                   class="form-control form-control-solid mb-3 mb-lg-0"
                                   placeholder="Project" value=""/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Machine</label>
                            <input type="hidden" name="device_id" id="device_id" value="">
                            <input type="text" name="machine" id="machine" placeholder="Machine" value=""
                                   class="form-control form-control-solid mb-3 mb-lg-0"/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Process</label>
                            <input type="text" name="process" id="process" placeholder="Process" value="{{ @$item->process }}"
                                   class="form-control form-control-solid mb-3 mb-lg-0"/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Version</label>
                            <input type="text" name="version" id="version" placeholder="Version" value="{{ @$item->version }}"
                                   class="form-control form-control-solid mb-3 mb-lg-0"/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Country</label>
                            <select class="form-control form-control-solid mb-3 mb-lg-0"
                                    name="country_id" id="country_id">
                                <option>please choose country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">City</label>
                            <select class="form-control form-control-solid mb-3 mb-lg-0" name="city_id" id="city_id">
                                <option>please choose country first</option>
                            </select>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Plus(millisecond)</label>
                            <input type="text" name="plus_millisecond" id="plus_millisecond"
                                   placeholder="Plus(millisecond)" value="{{ @$item->plus_millisecond }}"
                                   class="form-control form-control-solid mb-3 mb-lg-0"/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Produced parts per hour</label>
                            <input type="text" name="produced_parts_per_hour" id="produced_parts_per_hour"
                                   placeholder="Produced parts per hour" value="{{ @$item->produced_parts_per_hour }}"
                                   class="form-control form-control-solid mb-3 mb-lg-0"/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Second per pulse</label>
                            <input type="text" name="second_per_pulse" id="second_per_pulse"
                                   placeholder="Second per pulse" value="{{ @$item->second_per_pulse }}"
                                   class="form-control form-control-solid mb-3 mb-lg-0"/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Pieces per pulse</label>
                            <input type="text" name="pieces_per_pulse" id="pieces_per_pulse"
                                   placeholder="Pieces per pulse" value="{{ @$item->pieces_per_pulse }}"
                                   class="form-control form-control-solid mb-3 mb-lg-0"/>
                        </div>
                    </div>
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal"
                                data-kt-devices-modal-action="cancel">Discard
                        </button>
                        <button type="submit" class="btn btn-primary" id="new_device_btn"
                                data-kt-devices-modal-action="submit">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait...
                                <span
                                    class="spinner-border spinner-border-sm align-middle ms-2">
                                </span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
