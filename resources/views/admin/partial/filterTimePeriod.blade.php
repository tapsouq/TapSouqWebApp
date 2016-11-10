<form method="get" class="time-period-form">
    <div class="form row">
        <div class="col-md-4 col-md-offset-4 text-center">
            <div class="btn-group filter-group">
                <button type="button" class="btn btn-default" id="daterange-btn">
                    <span>
                        <i class="fa fa-calendar"></i>
                        {!! trans('admin.select_time_period') !!} 
                    </span>
                    <i class="fa fa-caret-down"></i>
                    <input type="hidden" name="from">
                    <input type="hidden" name="to">
                </button>
            </div>
        </div>
    </div>
</form>