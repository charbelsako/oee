<div class="modal fade" id="kt_modal_add_device" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_device_header">
                <h2 class="fw-bold">Add Device</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary"
                     data-kt-devices-modal-action="close">
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
                <form id="kt_modal_add_device_form" class="form" action="#">
                    @csrf
                    <div class="d-flex flex-column scroll-y me-n7 pe-7"
                         id="kt_modal_add_device_scroll" data-kt-scroll="true"
                         data-kt-scroll-activate="{default: false, lg: true}"
                         data-kt-scroll-max-height="auto"
                         data-kt-scroll-dependencies="#kt_modal_add_device_header"
                         data-kt-scroll-wrappers="#kt_modal_add_device_scroll"
                         data-kt-scroll-offset="300px">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Project</label>
                            <input type="text" name="project"
                                   class="form-control form-control-solid mb-3 mb-lg-0"
                                   placeholder="Project" value=""/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Machine</label>
                            <input type="text" name="machine"
                                   class="form-control form-control-solid mb-3 mb-lg-0"
                                   placeholder="Machine" value=""/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Process</label>
                            <input type="text" name="process"
                                   class="form-control form-control-solid mb-3 mb-lg-0"
                                   placeholder="Process" value=""/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Version</label>
                            <input type="text" name="version"
                                   class="form-control form-control-solid mb-3 mb-lg-0"
                                   placeholder="Version" value=""/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Country</label>
                            <select class="form-control form-control-solid mb-3 mb-lg-0" name="country_id" id="country_id">
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
                    </div>
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3"
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
