<div style="width:90%;float:left;padding:30px">


    <div style="width:100%;float:left;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#333;font-weight:bold;text-align:right;margin-bottom:30px">

        <a style="float:left" href="#" target="_blank"></a>

        <span class="aBn" data-term="goog_1606173504" tabindex="0"><span class="aQJ">{{date('F d, Y')}}</span></span>
    </div>


    <div style="width:100%;float:left;font-family:Arial,Helvetica,sans-serif;line-height:22px;font-size:15px;color:#333;margin:30px 0">


        <p style="margin:0;text-transform:capitalize">Dear {{$username}},</p>

        <p>Welcome to {{env('APP_FULL_NAME')}}</p>

        <p>Congratulation! Your Customer account has been registered to join 70doors.com. To begin Login and saving more money,
            Please Confirm Your Email to click the confirmation link below.</p>

        <p>We really hope that it will help to save money through its comprehensive listing of vouchers, coupons,
            promotions and discount deal’s on your online or offline purchase.</p>
        <br/>

        <p>White Bazar respects customer's privacy and will never sell, rent or share your information. If you continue
            to have problems signing up or activating your account, please contact us at <a href="#" target="_blank">support@70doors.com</a> or 02-345345345.</p>


        <br/><br/>

        <p style="margin:0">Best Regards,</p>
        <p style="margin:0">{{env('APP_FULL_NAME')}} Team</p>
        <br/>
        <div style="text-align: center;">
            <a href="{{url('/buyer-email-confirmation/'.$confirmation_code)}}" style="padding: 10px 20px; background-color: #FF8300; text-decoration: none;
        color: #ffffff;">Confirm Your Email</a>
            <br/><p>Or Copy this link to your browser:</p>
            <a href="{{url('/buyer-email-confirmation/'.$confirmation_code)}}">{{url('/buyer-email-confirmation/'.$confirmation_code)}}</a>
        </div>



    </div>


</div></div>