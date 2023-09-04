<div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_user_header">
                <h2 class="fw-bold">Add User</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary"
                     data-kt-users-modal-action="close">
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
                <form id="kt_modal_add_user_form" class="form" action="#">
                    @csrf
                    <div class="d-flex flex-column scroll-y me-n7 pe-7"
                         id="kt_modal_add_user_scroll" data-kt-scroll="true"
                         data-kt-scroll-activate="{default: false, lg: true}"
                         data-kt-scroll-max-height="auto"
                         data-kt-scroll-dependencies="#kt_modal_add_user_header"
                         data-kt-scroll-wrappers="#kt_modal_add_user_scroll"
                         data-kt-scroll-offset="300px">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Full Name</label>
                            <input type="text" name="name"
                                   class="form-control form-control-solid mb-3 mb-lg-0"
                                   placeholder="Full name" value=""/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Email</label>
                            <input type="email" name="email"
                                   class="form-control form-control-solid mb-3 mb-lg-0"
                                   placeholder="example@domain.com" value=""/>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Password</label>
                            <input type="password" name="password"
                                   class="form-control form-control-solid mb-3 mb-lg-0"
                                   placeholder="********" value=""/>
                        </div>
                        <div class="mb-7">
                            <label class="required fw-semibold fs-6 mb-5">Role</label>
                            <div class='separator separator-dashed my-5'></div>
                            <div class="d-flex fv-row">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input me-3" name="role"
                                           type="radio" value="0" id="kt_modal_update_role_option_0"
                                           checked='checked'/>
                                    <label class="form-check-label"
                                           for="kt_modal_update_role_option_0">
                                        <span class="fw-bold text-gray-800">Administrator</span><br/>
                                        <span class="text-gray-600">
                                            company administrators
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class='separator separator-dashed my-5'></div>
                            <div class="d-flex fv-row">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input me-3" name="role"
                                           type="radio" value="1"
                                           id="kt_modal_update_role_option_1"/>
                                    <label class="form-check-label"
                                           for="kt_modal_update_role_option_1">
                                        <span class="fw-bold text-gray-800">Editor</span><br/>
                                        <span class="text-gray-600">
                                            all permission without user managements
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class='separator separator-dashed my-5'></div>
                            <div class="d-flex fv-row">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input me-3" name="role"
                                           type="radio" value="2"
                                           id="kt_modal_update_role_option_2"/>
                                    <label class="form-check-label"
                                           for="kt_modal_update_role_option_2">
                                        <span class="fw-bold text-gray-800">Viewer</span><br/>
                                        <span class="text-gray-600">
                                            only view data
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3"
                                data-kt-users-modal-action="cancel">Discard
                        </button>
                        <button type="submit" class="btn btn-primary" id="new_user_btn"
                                data-kt-users-modal-action="submit">
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
