<style>
    .my_checkbox {
        margin-left: 10px !important;
        width: 15px;
        height: 15px;
    }
    * {
        word-wrap:break-word;
        word-break:break-all;
    }
    .alert {
        width: 100%;
        z-index: 10;
    }

    .swiper-container {
        width: 100%;
        height: 100%;
    }
    .swiper-slide {

        /* Center slide text vertically */
        display: -webkit-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        -webkit-align-items: center;
        align-items: center;
    }
    .swiper-pagination-bullet {
        width: 20px;
        height: 20px;
        text-align: center;
        line-height: 20px;
        font-size: 12px;
        color:#000;
        opacity: 1;
        background: rgba(0,0,0,0.2);
    }
    .swiper-pagination-bullet-active {
        color:#fff;
        background: #007aff;
    }
    .footer{
        text-align: center;
    }
</style>
<script>
    function del() {
        if (window.confirm('确定这个操作吗？')){
            return true;
        }else{
            return false;
        }
    }
    $(document).ready(function () {
        var onResize = function () {
            // apply dynamic padding at the top of the body according to the fixed navbar height
            //$("body").css("padding-left", $(".side-navbar").width());
            $("body").css("padding-bottom", $(".navbar-fixed-bottom").height());
        };

        // attach the function to the window resize event
        $(window).resize(onResize);
        onResize();
    })
</script>
<div class="footer">
    <hr>
    <p>
        Copyright 2022 <a href="/">书签网</a>
    </p>
</div>
</body>
</html>