<div class="">
    <form id="role_form">
        <div class="form-group">
            <label class="control-label text-left">Role Name</label>
            <div class="input-group">
                <input value="{{isset($item) ? $item->name : ''}}" type="text" id="role_name" class="form-control" name="role_name" required>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="row m-t-10">
            <div class="col-sm-12 custom-btn-center center-block">
                <a class="btn custom-btn-info p-x-4 m-x-2" href="{{ route('roles.index') }}">Cancel</a>
                <button id="role_btn" class="btn custom-btn-brand btn-md p-x-4 m-x-2" type="submit">Save</button>
            </div>
        </div>
    </form>
</div>
