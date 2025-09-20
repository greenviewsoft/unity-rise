<style>

  

    #snackbar {

        visibility: hidden;

    

        min-width: 135px !important;

        width: 30%;
        height: 170px;

        background-color: rgba(0, 0, 0, 0.7)!important;

    

        color: white;

    

        text-align: center;

    

        border-radius: 2px;

    

        padding: 5px;

    

        position: fixed;

    

        z-index: 999999;

    

        left: 50%;

    

        transform: translate(-50%, -50%);

    

        top: 60%!important;

    

        font-size: 14px;

    

        border-bottom-left-radius: 10px !important;

    

        border-bottom-right-radius: 10px !important;

    

        border-top-left-radius: 10px !important;

    

        border-top-right-radius: 10px !important;

    

        padding-top: 50px !important;

    

        padding-bottom: 6px !important;

    }

    

    #snackbar::before {

    

        position: absolute;

    

        top: 16px;

    

        left: 0;

    

        width: 100%;

    

        height: 100%;

    

        content: url('https://tsmc23.com/public/assets/user/images/right_mark.png');

    

    }

    

      #snackbar.show {

        visibility: visible;

        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;

        animation: fadein 0.5s, fadeout 0.5s 2.5s;

      }

    

      #snackbar2 {

    

        visibility: hidden;

    

        min-width: 135px !important;

    

        width: 30%;
        height: 170px;

    

        background-color: rgba(0, 0, 0, 0.7)!important;

    

        color: white;

    

        text-align: center;

    

        border-radius: 2px;

    

        padding: 5px;

    

        position: fixed;

    

        z-index: 999999;

    

        left: 50%;

    

        transform: translate(-50%, -50%);

    

        top: 60%!important;

    

        font-size: 14px;

    

        border-bottom-left-radius: 10px !important;

    

        border-bottom-right-radius: 10px !important;

    

        border-top-left-radius: 10px !important;

    

        border-top-right-radius: 10px !important;

    

        padding-top: 50px !important;

    

        padding-bottom: 6px !important;

    

      }

      

      #snackbar2::before {

    

        position: absolute;

    

        top: 16px;

    

        left: 0;

    

        width: 100%;

    

        height: 100%;

    

        content: url('https://tsmc23.com/public/assets/user/images/danger.png');

    

    }

    

      #snackbar2.show {

        visibility: visible;

        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;

        animation: fadein 0.5s, fadeout 0.5s 2.5s;

      }

    

      @-webkit-keyframes fadein {

        from {bottom: 0; opacity: 0;}

        to {bottom: 100px; opacity: 1;}

      }

    

      @keyframes fadein {

        from {bottom: 0; opacity: 0;}

        to {bottom: 100px; opacity: 1;}

      }

    

      @-webkit-keyframes fadeout {

        from {bottom: 100px; opacity: 1;}

        to {bottom: 0; opacity: 0;}

      }

    

      @keyframes fadeout {

        from {bottom: 100px; opacity: 1;}

        to {bottom: 0; opacity: 0;}

      }

    </style>

    