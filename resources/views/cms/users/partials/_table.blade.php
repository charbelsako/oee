<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
    <thead>
    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
        <th class="w-10px pe-2">
            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                <input class="form-check-input" type="checkbox" data-kt-check="true"
                       data-kt-check-target="#kt_table_users .form-check-input" value="1"/>
            </div>
        </th>
        <th class="min-w-125px">Name</th>
        <th class="min-w-125px">Email</th>
        <th class="min-w-125px">Role</th>
        <th class="min-w-125px">Joined Date</th>
        <th class="text-end min-w-100px">Actions</th>
    </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
    <tr>
        <td>
            <div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" value="1"/>
            </div>
        </td>
        <td>
            Emma Smith
        </td>
        <td>
            smith@kpmg.com
        </td>
        <td>Administrator</td>
        <td>05 May 2022, 6:43 am</td>
        <td class="text-end">
            <a href="#" class="btn btn-light btn-active-light-primary btn-sm"
               data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                <span class="svg-icon svg-icon-5 m-0">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                            fill="currentColor"/>
                                    </svg>
                                </span>
            </a>
            <div
                class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                data-kt-menu="true">
                <div class="menu-item px-3">
                    <a href="{{ route('users.edit',1) }}"
                       class="menu-link px-3">Edit</a>
                </div>
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3"
                       data-kt-users-table-filter="delete_row">Delete</a>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>
