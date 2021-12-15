@extends('frontend.master',['menu'=>'products'])
@section('title',__('messages.page_title.products'))
@section('stylesheet')
    <link rel="stylesheet" href="{{asset('build/css/separate/vendor/bootstrap-touchspin.min.css')}}">
    <style>
        .btn-file {
            position: relative;
            overflow: hidden;
            transition: all .2s ease-in-out;
            cursor: pointer;
            margin-right: 20px;
            padding: 4px 10px;
        }

        .btn-file input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            height: 200px;
            width: 1000px;
            opacity: 0;
            cursor: pointer;
        }

        .gallery-grid .gallery-col {
            float: left;
            width: 20%;
            padding: 7px;
        }

        .gallery-item {
            height: 150px;
        }

        .gallery-item {
            position: relative;
            -webkit-border-radius: 5px;
            border-radius: 5px;
            overflow: hidden;
        }

        .gallery-item .remove_from_wishlist {
            position: absolute;
            top: 6px;
            right: 6px;
            font-size: 32px;
            border: 1px solid #000;
            border-radius: 3px;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
        }

        .btn-sm {
            padding: 5px 10px;
        }

        .btn-sm + .btn-sm {
            margin-top: 2px;
        }

        a, a:focus {
            color: #000;
        }

        .label-primary {
            background-color: #46c35f;
        }

        #fixed_right {
            width: 250px;
            height: 150px;
            padding: 15px;
            position: fixed;
            top: 25%;
            right: 10px;
            display: none;
        }

        .loading_image {
            background-image: url('{{asset('image/pageloader.gif')}}');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: contain;
        }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="row">
                @include('frontend.seller.sidebar-nav',['menu'=>'products'])
                <div class="col-md-9 col-sm-8">
                    @if(!empty(Session::get('message')))
                        <div class="alert alert-success alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            {{Session::get('message')}}
                        </div>
                    @endif
                    @if(!empty(Session::get('error_message')))
                        <div class="alert alert-warning alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            {{Session::get('error_message')}}
                        </div>
                    @endif
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?php
                            $membership_plan = App\Model\Subscription::where('seller_id', Auth::user()->getSeller->id)->exists();
                            $disabled = false;
                            if (Auth::user()->getSeller->business_type == \App\Http\Controllers\Enum\ProductTypeEnum::SERVICE && $membership_plan == false) {
                                $disabled = true;
                            }
                            ?>
                            @lang('messages.seller.product.title_service')


                            @if($disabled == false)
                                <a href="#form_modal" data-toggle="modal" id="add_btn"
                                   data-seller="{{\Illuminate\Support\Facades\Crypt::encrypt(Auth::user()->getSeller->id)}}"
                                   data-id="add"
                                   class="btn btn-primary btn-sm pull-right">

                                    @lang('messages.seller.product.add_service')
                                </a>
                            @endif
                        </div>
                        <div class="panel-body">
                            <div class="shop">
                                @if(!empty($products[0]))
                                    <table class="shop_table cart wishlist_table">
                                        <thead>
                                        <tr>
                                            <th width="50">@lang('messages.seller.product.featured')</th>
                                            <th class="product-name"><span
                                                        class="nobr">@lang('messages.seller.product.details')</span>
                                            </th>
                                            <th class="product-stock-stauts"><span
                                                        class="nobr">@lang('messages.seller.product.price')</span></th>
                                            @if(Auth::user()->getSeller->business_type == \App\Http\Controllers\Enum\ProductTypeEnum::PRODUCT)
                                                <th class="product-stock-stauts" width="100"><span
                                                            class="nobr">@lang('messages.seller.product.quantity')</span>
                                                </th>
                                            @endif
                                            <th class="product-stock-stauts"><span
                                                        class="nobr">@lang('messages.seller.product.media')</span></th>
                                            <th class="product-add-to-cart" width="125"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($products as $product)
                                            <tr @if($product->quantity <=0)style="background-color: rgba(255, 0, 0, 0.12)" @endif>
                                                <td style="vertical-align: top; text-align: center">
                                                    @if(!empty($product->getFeaturedProduct) && $product->getFeaturedProduct->product_id == $product->id)
                                                        <span style="background-color: #46c35f;"
                                                              class="label label-custom label-pill label-success">@lang('messages.seller.product.featured') </span>
                                                    @else
                                                        <input type="checkbox" class="featured_product"
                                                               name="featured_product" value="{{$product->id}}">
                                                    @endif
                                                </td>
                                                <td class="product-name">
                                                    <span><strong>@lang('messages.seller.product.name'):</strong> <a
                                                                href="{{url('/product/details/'.$product->id)}}">{{$product->name}}</a></span>
                                                    @if(isset($product->getCategory))
                                                        <br><span><strong>@lang('messages.seller.product.category')
                                                                :</strong> <a
                                                                    href="{{url('/products/category/'.$product->getCategory->id)}}">{{$product->getCategory->name}}</a></span>
                                                    @endif
                                                    @if(!empty(count($product->getProductDeals)))
                                                        <br>
                                                        <span><strong>Active Deal:</strong> <?php if (count($product->getProductDeals) > 1) echo 's'; ?> {{count($product->getProductDeals)}}</span>
                                                    @endif
                                                </td>
                                                <td class="product-price">{{env('CURRENCY_SYMBOL').number_format($product->price,2)}}</td>
                                                @if(Auth::user()->getSeller->business_type == \App\Http\Controllers\Enum\ProductTypeEnum::PRODUCT)
                                                    <td class="product-price">
                                                        {!! Form::open(['url'=>'/seller/product/quantity','class'=>'quantity_form']) !!}
                                                        <div class="form-group" style="margin-bottom: 5px;">
                                                            <input class="quantity_input" type="text"
                                                                   value="{{$product->quantity}}" name="quantity">
                                                        </div>
                                                        <button type="submit"
                                                                class="btn btn-sm btn-block btn-primary">@lang('messages.seller.product.save')</button>
                                                        <input type="hidden" name="product" value="{{$product->id}}">
                                                        {!! Form::close() !!}
                                                    </td>
                                                @endif
                                                <td class="product-price">
                                                    <span class="tabledit-span">
                                                        <span class="label @if(!empty($product->getMedia->count())) label-primary @else label-default @endif media_count_{{$product->id}}">{{$product->getMedia->count()}}</span>
                                                        <a class="media_modal_popup" data-toggle="modal"
                                                           href="#media_form_modal"
                                                           data-id="{{$product->id}}">@lang('messages.seller.product.add')</a>
                                                    </span>
                                                </td>
                                                <td class="product-add-to-cart">
                                                  <span data-toggle="tooltip" data-placement="top"
                                                        title="@lang('messages.seller.product.edit')">
                                                    <a data-toggle="modal" href="#form_modal"
                                                       data-seller="{{\Illuminate\Support\Facades\Crypt::encrypt(Auth::user()->getSeller->id)}}"
                                                       data-id="{{$product->id}}"
                                                       class="edit_btn btn-sm btn btn-primary"><i
                                                                class="fa  fa-pencil-square-o"></i></a>
                                                  </span>

                                                    <span data-toggle="tooltip" data-placement="top"
                                                          title="@lang('messages.seller.product.promotion_deal')">
                                                        <a href="{{url('/seller/product/deal/'.$product->id)}}"
                                                           class="edit_btn btn btn-sm btn-primary"><i
                                                                    class="fa fa-tags"></i></a>
                                                      </span>

                                                    <?php
                                                    $order_item_exist = \App\Model\OrderItem::where('product_id', $product->id)->exists();
                                                    ?>


                                                    <span data-toggle="tooltip" data-placement="top"
                                                          title="@if(!$order_item_exist) @lang('messages.seller.product.delete') @else Product has orders, not allow to delete. @endif">
                                                        <a @if($order_item_exist) disabled=""
                                                           @endif href="#delete_model_{{$product->id}}"
                                                           data-toggle="modal"
                                                           class="btn btn-sm  btn-primary"><i
                                                                    class="fa fa-trash"></i></a>

                                                        @if(!$order_item_exist)
                                                            <div class="modal fade" id="delete_model_{{$product->id}}"
                                                                 tabindex="-1" role="dialog"
                                                                 aria-labelledby="mySmallModalLabel">
                                                            <div class="modal-dialog modal-sm" role="document"
                                                                 style="margin-top: 15%;">
                                                            <div class="modal-content">
                                                            <div class="modal-header">
                                                            <h4 class="modal-title">@lang('messages.seller.product.do_you_want_to_delete')</h4>
                                                            </div>
                                                            <div class="modal-body text-center">

                                                            <a href="#" data-dismiss="modal"
                                                               class="btn btn-sm btn-default">@lang('messages.seller.product.no')</a>
                                                                <a href="{{url('/seller/product/delete/'.$product->id)}}"
                                                                   class="btn btn-sm btn-primary">@lang('messages.seller.product.yes')</a>
                                                            </div>
                                                            </div>
                                                            </div>
                                                            </div>
                                                        @endif
                                                    </span>

                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-md-12"><h4>@lang('messages.seller.no_product_available')</h4></div>
                                @endif
                            </div>
                            <div id="pagination">
                                @include('frontend.widget.pagination',['paginator'=>$products])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="form_modal" data-backdrop="true" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title product-modal-title" id="myModalLabel">Modal title</h4>
                </div>
                {!! Form::open(array('url'=>'/seller/product/save','id'=>'modal_form','files'=>true)) !!}
                <div class="modal-body" id="modal_form_generate">
                    <div class="text-center load_image" style="margin-top: 23px;">
                        <img src="{{asset('build/img/ring-alt.gif')}}" style="width:50px;"
                             alt="">

                        <div>Loading</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('messages.seller.product.close')</button>
                    <button type="submit" class="btn btn-primary"
                            id="submit_btn">@lang('messages.seller.product.save')</button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" id="media_form_modal" data-backdrop="true" tabindex="-1" role="dialog"
         aria-labelledby="mediaModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {!! Form::open(array('url'=>'/seller/product/media/save','id'=>'media_modal_form','files'=>true)) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="mediaModalLabel">@lang('messages.seller.product.upload_media')
                        <span class="btn btn-primary btn-file pull-right">
                            <span>@lang('messages.seller.product.add_media')</span>
                            <input accept="image/*" type="file" class="media_upload" name="photo[]" multiple value="">
                        </span>
                    </h4>
                </div>

                <div class="modal-body" id="media_modal_form_generate"
                     style="height: 440px; overflow-x: initial; width: 100%; overflow-y: scroll;">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="media_progress" style="display:none;">
                                    <progress class="progress" value="0" max="100">
                                        <div class="progress">
                                            <span class="progress-bar" id="progress-bar" style="width: 0%;">0%</span>
                                        </div>
                                    </progress>
                                    <div class="uploading-list-item-progress">0%</div>
                                </div>
                                <div class="gallery-grid" id="add_ajax_product_id">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="product_id" id="product_id_media" value="1">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('messages.seller.product.close')</button>
                    <button type="submit" onclick="this.form.submit()" class="btn btn-primary"
                            id="media_submit_btn">@lang('messages.seller.product.save')</button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
    {!! Form::open(['url'=>'/seller/product/featured']) !!}
    <div id="fixed_right">
        <?php $featured_product_feas = App\Model\Setting::where('key', App\Http\Controllers\Enum\SettingsEnum::FEATURED_PRODUCT_SUBSCRIPTION_FEE)->first(); ?>
        <div class="panel panel-default">
            <div class="panel-heading" style="font-size: 16px; line-height: 16px;">
                @lang('messages.featured_services')
            </div>
            <div class="panel-body">
                <h4><span id="product_count">0</span> X <span
                            id="featured_product_feas">{{$featured_product_feas->value}}</span> = <span
                            id="total">0</span></p></h4>
                <button type="submit" class="btn btn-primary btn-sm btn-block"
                        name="button">@lang('messages.pay')</button>
                <input type="hidden" name="feature_product" id="feature_product" value="">
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop

@section('script')
    {!! \App\UtilityFunction::getToastrMessage(Session::get('TOASTR_MESSAGE'))!!}
    <script src="{{asset('build/js/lib/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js')}}"></script>
    <script>
        function getTouchSpin() {
            $(".quantity_input").TouchSpin({
                verticalbuttons: true,
                verticalupclass: 'glyphicon glyphicon-plus',
                verticaldownclass: 'glyphicon glyphicon-minus',
                step: 1,
                decimals: 0,
                max: 1000000000,
            });
        }
        getTouchSpin();
    </script>
    <script>
        // Featured product calculation
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        $(document.body).on('click', '.featured_product', function () {
            var product_id = $(this).val();
            var featured_product_feas = {{$featured_product_feas->value}};
            $('#featured_product_feas').html(featured_product_feas);

            var id = $("[type=checkbox][name=featured_product]:checked").map(function () {
                return this.value;
            }).get().join(",");

            var product_count = (id.split(",").length);

            if (product_count > 0 && id != '') {
                $('#fixed_right').show("slide", {direction: "right"}, 250);
                $('#product_count').html(product_count);

                var total_price = numberWithCommas((featured_product_feas * product_count).toFixed(2));

                $('#total').html(total_price);
                $('#feature_product').val(id);
            }
            else {
                $('#fixed_right').hide("slide", {direction: "right"}, 250);
            }
        });

        // quantity
        $(document.body).on('submit', '.quantity_form', function (e) {
            var form = $(this);
            $(this).children('button').addClass('loading_image').prop('disabled', true);
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: $(form).attr('action'),
                data: $(form).serialize(),
                dataType: "json",
                context: this,
                success: function (data) {
                    $(this).children('button').removeClass('loading_image').prop('disabled', false);
                    toastr.success('@lang('messages.seller.product.quantity_save_success')');
                }
            }).fail(function (data) {
                toastr.warning('@lang('messages.seller.product.quantity_save_failed')');
                $(this).children('button').removeClass('loading_image').prop('disabled', false);
                var errors = data.responseJSON;
                console.log(errors);
            });
        });

        $(document.body).on('click', '.delete_image', function () {
            var media_image_id = $(this).data('id');
            $.ajax({
                type: "GET",
                url: '{{url('/admin/product/seller/product/media/delete')}}' + '?media_image_id=' + media_image_id,
                dataType: "json",
                success: function (data) {
                    $('.media_' + media_image_id).remove();
                    toastr.success(data.message);
                    $('.media_count_' + data.product_id).html(data.media_count);
                    if (data.media_count == 0) {
                        $('#add_ajax_product_id').html('<span class="text-center text-danger">No Media File Exist!!!</span>');
                        $('.media_count_' + data.product_id).removeClass('label-danger');
                        $('.media_count_' + data.product_id).addClass('label-default');
                    }
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            });
        });
        $(document.body).on('click', '.media_modal_popup', function () {
            $('#product_id_media').val('')
            var product_id = $(this).data('id');
            $('#product_id_media').val(product_id);
            $('#add_ajax_product_id').append('<div class="text-center"><img src="{{asset('/build/img/ring-alt.gif')}}" height="100" ></div>');
            $.ajax({
                type: "GET",
                url: '{{url('/seller/product/media/save')}}' + '?edit_id=edit_id&product_id=' + product_id,
                dataType: "json",
                success: function (data) {
                    $('#add_ajax_product_id').empty();
                    $('#add_ajax_product_id').append(data.data_generate);
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            });
        });
        $(document).ready(function () {
            $(document.body).on('submit', '#media_modal_form', function (e) {
                if ($('#skip').val() == 0) {
                    $("#media_progress").show();
                    $(".progress").val(0);
                    $('#add_ajax_product_id').empty();
                    $('#add_ajax_product_id').append('<div class="text-center"><img src="{{asset('/build/img/ring-alt.gif')}}" height="100" ></div>');
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: $('#media_modal_form').attr('action') + '?add_id=add_id',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        xhr: function () {
                            //upload Progress
                            var xhr = $.ajaxSettings.xhr();
                            if (xhr.upload) {
                                xhr.upload.addEventListener('progress', function (event) {
                                    var percent = 0;
                                    var position = event.loaded || event.position;
                                    var total = event.total;
                                    if (event.lengthComputable) {
                                        percent = Math.ceil(position / total * 100);
                                    }
                                    //update progressbar

                                    $(".progress").val(percent);
                                    $("#progress-bar").css("width", +percent + "%");
                                    $(".uploading-list-item-progress").html(percent + "%");
                                }, true);
                            }
                            return xhr;
                        },
                        mimeType: "multipart/form-data"
                    }).done(function (data) { //
                        // $(my_form_id)[0].reset(); //reset form
                        // $(result_output).html(res); //output response from server
                        var data_arr = JSON.parse(data);
                        $('#add_ajax_product_id').empty();
                        $('#add_ajax_product_id').append(data_arr.data_generate);
                        $('.media_count_' + data_arr.product_id).html(data_arr.media_count);
                        $('.media_count_' + data_arr.product_id).removeClass('label-default');
                        $('.media_count_' + data_arr.product_id).addClass('label-danger');
                        $("#media_progress").hide();
                    });
                }

            });
            $(document.body).on('change', '.media_upload', function () {
                $('#skip').val(0);
                $('#media_modal_form').submit();
            });
        });
        $(document.body).on('click', '.delete_image', function () {
            var media_image_id = $(this).data('id');
            $.ajax({
                type: "GET",
                url: '{{url('/seller/product/media/delete')}}' + '?media_image_id=' + media_image_id,
                dataType: "json",
                success: function (data) {
                    $('.media_' + media_image_id).remove();
                    toastr.success(data.message);
                    $('.media_count_' + data.product_id).html(data.media_count);
                    if (data.media_count == 0) {
                        $('#add_ajax_product_id').html('<span class="text-center text-danger">No Media File Exist!!!</span>');
                        $('.media_count_' + data.product_id).removeClass('label-danger');
                        $('.media_count_' + data.product_id).addClass('label-default');
                    }
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            });
        });
        $(document).ready(function () {
            $("#add_btn").click(function () {
                var id = $(this).data('id');
                var seller_id = $(this).data('seller');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "POST",
                    url: $('#modal_form').attr('action') + '?add_id=' + id + '&seller_id=' + seller_id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.product-modal-title').html('@lang('messages.seller.product.product_add')');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();

                        getTouchSpin();
                        textareaMaxLength();
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
            $(".edit_btn").click(function () {
                var id = $(this).data('id');
                var seller_id = $(this).data('seller');
                $("#model_body").empty();
                $('.load_image').show();
                $.ajax({
                    type: "POST",
                    url: $('#modal_form').attr('action') + '?edit_id=' + id + '&seller_id=' + seller_id,
                    data: $('#modal_form').serialize(),
                    dataType: "json",
                    success: function (data) {
                        $('.product-modal-title').html('@lang('messages.seller.product.product_add')');
                        $("#modal_form_generate").html(data.data_generate);
                        $('.load_image').hide();

                        getTouchSpin();
                        textareaMaxLength();
                    }
                }).fail(function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                });
            });
        });
    </script>
@stop
