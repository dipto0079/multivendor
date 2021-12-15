@extends('frontend.master',['menu'=>'stores'])
@section('title',__('messages.page_title.stores'))
@section('stylesheet')
    <style>
        .favorite_store_div {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #FF8300;
            z-index: 100;
        }

        .favorite_store_div a {
            color: #FF8300;
            font-size: 20px;
        }

        .store-com-box {
            position: relative;
        }

        .favorite_store_div:hover .fa-heart-o:before {
            content: "\f004";
        }

        .favorite_store_div:hover .fa-heart:before {
            content: "\f08a";
        }

        .center-block {
            min-height: 145px;
        }
    </style>
@stop

@section('content')
    <div id="main" class="site-main">
        <div class="container">
            <div class="col-lg-12 store-title-section top30">

                <div class="">
                    <div class="col-sm-5 store-title">
                        <h3>@lang('messages.stores.stores')</h3>
                    </div>

                    <div class="col-sm-7">

                        {!! Form::open(['url'=>'/stores','method'=>'get','id'=>'search_form']) !!}
                        <div id="top_search2" class="form-group form-group-cus" role="form">
                            <div class="form-group  col-sm-1 col-xcus-1" style="width: initial;">
                                <button id="company_name_btn" type="submit"
                                        class="btn btn-primary font-additional btn-custom-search" data-original-title=""
                                        title="">@lang('messages.stores.search')
                                </button>
                            </div>
                            <div class="form-group col-sm-3 col-cus-3 input-width">
                                <select name="filter_category" id="filter_category"
                                        class="form-control selectWidth form-cus">
                                    <option value="">@lang('messages.stores.category')</option>
                                    @foreach($categories as $category)
                                        <?php $first_step_category = \App\Model\ProductCategory::where('parent_category_id', $category->id)->get() ?>
                                        <option @if(Request::get('filter_category') == $category->id) selected
                                                @endif value="{{$category->id}}">@if(\App\UtilityFunction::getLocal()== "en") {{$category->name}} @else {{$category->ar_name}} @endif</option>

                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-3 col-cus-3 input-width">
                                <select name="store_category" id="store_category"
                                        class="form-control selectWidth form-cus" onchange="changeBusinessTpe()">
                                    <option @if(Request::get('store_category') == App\Http\Controllers\Enum\ProductTypeEnum::PRODUCT) selected
                                            @endif value="{{App\Http\Controllers\Enum\ProductTypeEnum::PRODUCT}}">@lang('messages.product')</option>
                                    <option @if(Request::get('store_category') == App\Http\Controllers\Enum\ProductTypeEnum::SERVICE) selected
                                            @endif value="{{App\Http\Controllers\Enum\ProductTypeEnum::SERVICE}}">@lang('messages.service')</option>
                                </select>
                            </div>
                            <script type="text/javascript">
                              function changeBusinessTpe() {
                                document.getElementById('filter_category').value = '';
                                document.getElementById('filter_search').value = '';
                                document.getElementById('search_form').submit();
                              }
                            </script>
                            <div class="form-group col-sm-5 col-cus-3 search-input-key">
                                <input type="text" name="filter_search" id="filter_search" class="form-control form-cus"
                                       value="{{Request::get('filter_search')}}"
                                       placeholder="@lang('messages.stores.type_keyword')">
                            </div>

                        </div>
                        {!! Form::close() !!}
                    </div>


                </div>
            </div>
            <div class="col-lg-12">
                <div class="alp-search-title">
                    <p>@lang('messages.stores.locate_txt')</p>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="alpha-search">
                    <ul>
                        <?php
                        $filter_store = $letter;
                        if (!empty(Request::get('filter_store'))) $filter_store = Request::get('filter_store');
                        ?>

                        @foreach (range('A', 'Z') as $letter)
                            <li @if($filter_store == $letter) class="active" @endif><a
                                        href="{{url('/stores?filter_store='.$letter)}}">{{$letter}}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-lg-12">
                <div class="row">
                    @if(!empty($sellers[0]))
                        @foreach($sellers as $seller)
                            <div class="col-lg-2 col-sm-3 col-xs-4 res-store">
                                <div class="store-com-box ribbon">
                                  <span class="build">
                                    @if($seller->business_type == App\Http\Controllers\Enum\ProductTypeEnum::PRODUCT) @lang('messages.product')
                                      @elseif($seller->business_type == App\Http\Controllers\Enum\ProductTypeEnum::SERVICE) @lang('messages.service') @endif
                                  </span>
                                    <span class="favorite_store_div">
                                        <a href="javascript:;" class="favorite_store"
                                           data-id="{{base64_encode($seller->id)}}">
                                            <i @if(App\Model\FavoriteStore::isExistInTheList($seller->id, $favorite_stores)==1) class="fa fa-heart"
                                               @else class="fa fa-heart-o" @endif></i></a></span>

                                    <div class="store-march-img">
                                        <a title="" href="{{url('store/'.$seller->store_name)}}">
                                            <img class="img-responsive center-block img_background"
                                                 src="<?=Image::url(asset('/image/default.jpg'), 200, 175, ['crop'])?>"
                                                 @if(!empty($seller->getUser->photo)) data-src="<?=Image::url(asset(env('USER_PHOTO_PATH') . $seller->getUser->photo), 200, 175, ['crop'])?>"
                                                 @else src="<?=Image::url(asset('image/no-media.jpg'), 200, 175, ['crop'])?>"
                                                 alt=""
                                                    @endif ></a>
                                    </div>
                                    <div class="store-link">
                                        <a href="{{url('store/'.$seller->store_name)}}"
                                           class="">{{$seller->store_name}}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <h4 class="text-center">@lang('messages.nothing_found')</h4>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        $(document.body).on('click', '.favorite_store', function (e) {
            var seller_id = $(this).data('id');
            $.ajax({
                type: "GET",
                url: '{{url('/add-to-favorite-store')}}?seller_id=' + seller_id,
                dataType: 'json',
                context: this,
                success: function (data) {
                    if (data.success == true) {
                        if (data.exists == 1) {
                            $(this).find('i.fa').removeClass('fa-heart-o');
                            $(this).find('i.fa').addClass('fa-heart');
                            toastr.success('@lang('messages.error_message.store_added_successfully')');
                        } else {
                            toastr.warning('@lang('messages.error_message.this_store_is_already_in_your_favorite_list')');
                        }
                    } else {
                        window.location.replace('{{url('/buyer/login')}}');
                    }
                }
            }).fail(function (data) {
                var errors = data.responseJSON;
            });
        });
        // $(document.body).on('change', '.store_category', function (e) {
        //     var store_category = $(this).data('id');
        //     $('#skip').val(1);
        //     $.ajax({
        //         type: "GET",
        //         url: '{{url('/stores')}}?store_category=' + store_category,
        //         dataType: 'json',
        //         context:this,
        //         success: function (data) {
        //           alert(data);
        //           $('#skip').val(0);
        //         }
        //     }).fail(function (data) {
        //         var errors = data.responseJSON;
        //         $('#skip').val(0);
        //     });
        // });
    </script>
@stop
