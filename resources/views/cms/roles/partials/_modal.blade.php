<div class="modal fade" id="kt_modal_add_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_role_header">
                <h2 class="fw-bold">Add Role</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary"
                     data-kt-roles-modal-action="close" data-bs-dismiss="modal">
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
                <form id="kt_modal_add_role_form" class="form" action="{{ route('roles.store') }}">
                    <div class="d-flex flex-column scroll-y me-n7 pe-7"
                         id="kt_modal_add_role_scroll" data-kt-scroll="true"
                         data-kt-scroll-activate="{default: false, lg: true}"
                         data-kt-scroll-max-height="auto"
                         data-kt-scroll-dependencies="#kt_modal_add_role_header"
                         data-kt-scroll-wrappers="#kt_modal_add_role_scroll"
                         data-kt-scroll-offset="300px">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Role Name</label>
                            <div class="input-group">
                                <input value="" type="hidden" id="role_id" class="form-control" name="role_id" required>
                                <input value="" type="text" id="name" class="form-control" name="name" required>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal"
                                data-kt-roles-modal-action="cancel">Discard
                        </button>
                        <button type="submit" class="btn btn-primary" id="new_role_btn"
                                data-kt-roles-modal-action="submit">
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
