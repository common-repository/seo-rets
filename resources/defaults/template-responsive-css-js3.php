<style>
    .search-nearby-places {
        position: absolute;
        top: 15px;
        z-index: 1;
        padding: 10px;
        right: 15px;
        width: 200px;
        height: 300px;
        background-color: #ffffff;
        -webkit-box-shadow: -5px 5px 5px 0px rgba(77, 77, 77, 0.6);
        -moz-box-shadow: -5px 5px 5px 0px rgba(77, 77, 77, 0.6);
        box-shadow: -5px 5px 5px 0px rgba(77, 77, 77, 0.6);
    }

    .map-section {
        position: relative;
    }

    .top-information-block {
        line-height: 75px;
        text-align: center;
        background-color: #f5f9fc;
    }

    #detail-tab {
        padding-bottom: 15px;
        position: relative;
    }

    .second-block {
        border-right: 1px solid #d5dbe0;
        border-left: 1px solid #d5dbe0;
    }

    .price-block {
        background-color: #2f96d1;
        color: #ffffff;
    }

    a:focus {
        outline: none !important;
    }

    #detail-tab ul.menu-tabs li.active a {
        background-color: #2f96d1;
    }

    .features_block ul li, .search-nearby-places ul li {
        list-style: none;
    }

    .search-nearby-places ul {
        -webkit-padding-start: 0;
    }

    .search-nearby-places ul li {
        line-height: 30px;
    }

    .search-nearby-places input, .search-nearby-places label {
        margin: 0;
    }

    .features_block ul li i {
        color: #2f96d1;
        margin-right: 5px;
    }

    #detail-tab ul.menu-tabs a span {
        -webkit-transition: all 0.3s;
        -moz-transition: all 0.3s;
        transition: all 0.3s;
    }

    #detail-tab ul.menu-tabs {
        float: right;
        width: 50px;
        margin: 0 0 0 10px;
        list-style: none;
        position: absolute;
        right: 15px;
        top: 15px;
        z-index: 100;
        box-sizing: content-box !important;
        -webkit-padding-start: 0;
    }

    #detail-tab ul.menu-tabs li + li {
        margin-top: 5px;
    }

    #detail-tab ul.menu-tabs a {
        display: block;
        height: 50px;
        line-height: 50px;
        text-align: center;
        color: #fff;
        background-color: #565f66;
        font-size: 18px;
        position: relative;
        border-radius: 5px;
    }

    #detail-tab ul.menu-tabs a .title {
        display: block;
        visibility: hidden;
        opacity: 0;
        font-size: 12px;
        position: absolute;
        right: 100%;
        margin-right: 1px;
        top: 0;
        text-transform: uppercase;
        overflow: hidden;
    }

    #detail-tab ul.menu-tabs a .title > span {
        display: block;
        padding: 0 10px;
        background-color: #565f66;
        transform: translateX(100%);
        -webkit-transform: translateX(100%);
        white-space: nowrap; /* text-overflow: ellipsis;*/
    }

    #detail-tab ul.menu-tabs li:hover a .title {
        visibility: visible;
        opacity: 1;
    }

    #detail-tab ul.menu-tabs li:hover a .title > span {
        transform: translateX(0);
        -webkit-transform: translateX(0);
    }

    #detail-tab .tab-content {
        overflow: hidden;
        padding: 0
    }

    #detail-tab .epl-tab-section {
        margin-top: 0;
    }

    #detail-tab .epl-video-container {
        height: 550px;
        padding: 0;
    }

    #detail-tab .epl-small-image {
        margin-top: 10px;
    }

    #detail-tab .epl-small-image .item {
        opacity: 0.2
    }

    #detail-tab .epl-small-image .active .item {
        opacity: 1
    }

    .sr-listing-pg img {
        max-width: initial;
    }

    .sr-listing-title {
        font-size: 15px;
    }

    .sr-info-section {
        font-size: 16px;
    }

    .sr-listing-title-price {
        font-size: 17px;
        color: #98bd15;
    }

    .sr-listing-title {
        background-color: #f5f5f5;
    }

    .sr-float-right {
        text-align: right;
    }

    /* Comments */

    .comments-area {
        display: none;
    }

    .menu-tabs LI {
        list-style-type: none !important;
        margin: 0 0 2px 0 !important;
    }
</style>