@extends('layouts.admin_layout')
@section('title', 'Export Report')
@section('content_page')
    @php
        date_default_timezone_set('Asia/Manila');
    @endphp
    <div class="content-wrapper">
        <section class="content p-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Export Report</h5>
                            </div>
                            <div class="card-body">
                                @if(session()->has('message'))
                                    <div class="alert alert-danger">
                                        <strong>{{ session()->get('message') }}</strong>
                                    </div>
                                @endif
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">Factory:</span>
                                    </div>
                                    <select class="form-control select2bs5" id="slctFactory" name="factory">
                                        <option value="" selected disabled>--- Select Factory ---</option>
                                        <option value="F1">Factory 1 & 2 - Cabuyao</option>
                                        <option value="F3">Factory 3 - Malvar</option>
                                    </select>    
                                </div>

                                {{-- <div class="input-group mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">Route:</span>
                                    </div>
                                    <select class="form-control select2bs5 get-equipment-model" id="slctRoute" name="route"></select>    
                                </div> --}}

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">Incoming:</span>
                                    </div>
                                    <input type="text" class="form-control datetimepicker" name="incoming" id="incoming" placeholder="Incoming">
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">Outgoing:</span>
                                    </div>
                                    <input type="text" class="form-control datetimepicker" id="outgoing" name="outgoing" placeholder="Outgoing">
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">From:</span>
                                    </div>
                                    <input type="date" class="form-control" id="dateFrom"  name="from" max="<?= date('Y-m-d'); ?>">
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend w-50">
                                        <span class="input-group-text w-100">To:</span>
                                    </div>
                                    <input type="date" class="form-control" id="dateTo" name="to" max="<?= date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-dark float-right" id="btnExportReport"><i class="fas fa-file-excel"></i> Export</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

<!-- JS CONTENT --}} -->
@section('js_content')
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2bs5').select2({
                theme: 'bootstrap-5'
            })          

            // GetEquipment($('.get-equipment'))

            $('#incoming').datetimepicker({
                datepicker:false,
                formatTime: 'h:iA', // if input value is empty, timepicker set time use defaultTime
                allowTimes: [
                    '07:30',
                    '19:30',
                ],
                validateOnBlur:false, // Verify datetime value from input, when losing focus. If value is not valid datetime, then to value inserts the current datetime
                onSelectTime:function(ct,$input){
                    $('#incoming').val(moment($input.val()).format('h:mmA'));
                },
            });

            $('#outgoing').datetimepicker({
                datepicker:false,
                formatTime: 'h:iA', // if input value is empty, timepicker set time use defaultTime
                allowTimes: [
                    '07:30',
                    '15:30',
                    '16:30',
                    '19:30',
                ],
                validateOnBlur:false, // Verify datetime value from input, when losing focus. If value is not valid datetime, then to value inserts the current datetime
                onSelectTime:function(ct,$input){
                    $('#outgoing').val(moment($input.val()).format('h:mmA'));
                },
            });

            $('#btnExportReport').on('click', function(){
                let factory     = $('#slctFactory').val()
                let route       = $('#slctRoute').val()
                let incoming    = $('#incoming').val()
                let outgoing    = $('#outgoing').val()
                let from        = $('#dateFrom').val()
                let to          = $('#dateTo').val()
                let url_route

                if(factory == null){
                    console.log('factory',factory)
                    alert('Select Factory')
                }else if(route == ''){
                    console.log('route',route)
                    alert('Select Route')
                }else if(incoming == ''){
                    console.log('incoming',incoming)
                    alert('Set Incoming')
                }else if(outgoing == ''){
                    console.log('outgoing',outgoing)
                    alert('Set Outgoing')
                }else if(from == ''){
                    console.log('from',from)
                    alert('Select Date From')
                }else if(to == ''){
                    console.log('to',to)
                    alert('Select Date To')
                }else{
                    if(route != null){
                        route.replace('/','||')
                        url_route = encodeURIComponent(route);
                    }else{
                        url_route = 'null';
                    }

                    window.location.href = `export_v2/${factory}/${url_route}/${incoming}/${outgoing}/${from}/${to}`;
                    console.log('export',incoming)
                    $('.alert').remove();
                }
            });
        });
    </script>
@endsection

