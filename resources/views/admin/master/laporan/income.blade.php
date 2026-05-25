@extends('layouts.app')
@section('title',__("income report"))

@section('content')

<x-head-datatable/>

<div class="container-fluid">

    <div class="row">

        <div class="col-lg-12">

            <div class="card w-100">

                <div class="card-header row">

                    <div class="row w-100">

                        <div class="col-lg-6 w-100">

                            <div class="row">

                                <div class="col-sm-4">

                                    <div class="form-group">
                                        <label>{{ __("month") }}</label>

                                        <input type="month"
                                               name="month"
                                               class="form-control">
                                    </div>

                                </div>

                                <div class="col-sm-4 pt-4">

                                    <button class="btn btn-primary font-weight-bold m-1 mt-1"
                                            id="filter">

                                        <i class="fas fa-filter m-1"></i>
                                        {{ __("filter") }}

                                    </button>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-6 w-100 d-flex justify-content-end align-items-center">

                            <button class="btn btn-outline-primary font-weight-bold m-1"
                                    id="print">

                                <i class="fas fa-print m-1"></i>
                                {{ __("print") }}

                            </button>

                            <button class="btn btn-outline-danger font-weight-bold m-1"
                                    id="export-pdf">

                                <i class="fas fa-file-pdf m-1"></i>
                                {{ __("messages.export-to", ["file" => "pdf"]) }}

                            </button>

                            <button class="btn btn-outline-success font-weight-bold m-1"
                                    id="export-excel">

                                <i class="fas fa-file-excel m-1"></i>
                                {{ __("messages.export-to", ["file" => "excel"]) }}

                            </button>

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="data-tabel"
                               width="100%"
                               class="table table-bordered text-nowrap border-bottom">

                            <thead>

                                <tr>

                                    <th width="8%">{{ __('no') }}</th>

                                    <th>{{ __('month') }}</th>

                                    <th>{{ __('income') }}</th>

                                    <th>{{ __('expense') }}</th>

                                    <th>{{ __('total profit') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                <tr>

                                    <td>1</td>

                                    <td>{{ $bulan }}</td>

                                    <td>
                                        Rp {{ number_format($pendapatan,0,',','.') }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($pengeluaran,0,',','.') }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($total,0,',','.') }}
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<x-data-table/>

<script>

$(document).ready(function(){

    const tabel = $('#data-tabel').DataTable({

        lengthChange:true,

        buttons:[
            {
                extend:'excel',
                className:'buttons-excel'
            },
            {
                extend:'print',
                className:'buttons-print'
            },
            {
                extend:'pdf',
                className:'buttons-pdf'
            }
        ]

    });

    $("#print").on('click',function(){
        tabel.button(".buttons-print").trigger();
    });

    $("#export-pdf").on('click',function(){
        tabel.button(".buttons-pdf").trigger();
    });

    $("#export-excel").on('click',function(){
        tabel.button(".buttons-excel").trigger();
    });

    $("#filter").on('click', function(){

        let month = $("input[name='month']").val();

        if(month){
            window.location.href = "?month=" + month;
        }

    });

});

</script>

@endsection