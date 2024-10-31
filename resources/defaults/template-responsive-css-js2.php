<style>
    .sr-listing-pg img {
        max-width: initial;
    }

    .sr-listing-image {
        display: table;
    }

    .sr-listing-descr {
        display: table-cell;
    }

    .sr-listing-image {
        float: left;
        margin-right: 10px;
    }

    .sr-listing-descr {

    }

    .sr-listing-clear {
        clear: both;
    }

    .basic_info {
        width: 40%;
    }

    .description {
        float: none;
        margin-left: 0 !important;
        text-align: left !important;
    }

    .sr-thumbs {
        margin-left: auto;
        margin-right: auto;
    }

    .sr-listing-photos {
        float: right;
        width: 50%;
        text-align: left;
        min-width: 285px;
        max-width: 320px;
    }

    .smallScreen {
        display: none;
    }

    .sr-listing-photo-details-main {
        height: auto;
        width: 315px;
        max-height: 236px;
    }

    #page .sr-listing {
        margin-bottom: 50px;
    }

    #primary .sr-listing-photos {
        width: 58%
    }

    #primary .sr-listing-det-buttons {
        width: 48%;
    }

    .backButton {
        width: 20px;
    }

    /* media styles*/

    @media only screen
    and (max-width: 940px) {
        .sr-listing-det-buttons img {
            width: 47%;
        }

        .sr-listing-photo-details-main {
            width: 95%;
        }

        .sr-thumbs a img {
            width: 30% !important;
            height: auto;
        }

    }

    @media only screen
    and (max-width: 760px) {
        .sr-listing-photo {
            height: auto;
        }

        .sr-listing {
            min-width: 0;
        }

        .sr-listing-image {
            float: none;
            text-align: center;
            display: block;
        }

        .sr-listing-descr {
            display: inline;
            text-align: center;
        }

        .srm-pages {
            min-width: 0;
        }

        .sr-listing {
            min-width: 0;
        }

        div.widget-area {
            margin-left: 10px;
        }

        .smallScreen {
            display: block;
        }

        .bigScreen {
            display: none;
        }
    }

    @media only screen
    and (max-width: 635px) {
        div.description {
            float: right;
        }
    }

    @media only screen
    and (max-width: 725px) {
        .sr-listing-photos {
            float: none;
        }

        .basic_info {
            float: none;
            width: 50%;
            margin-right: auto;
            margin-left: auto;
        }

        div.sr-listing-photos {
            float: none;
            margin-right: auto;
            margin-left: auto;
        }
    }

    @media only screen
    and (max-width: 545px) {
        .sr-listing-photo-details-main {
            min-width: 216px;
        }

        .sr-thumbs a img {
            min-width: 68px;
        }

        .sr-listing-image {
            float: none;
            text-align: center;
        }

        .sr-listing-det-buttons img {
            min-width: 107px;
        }

        .sr-listing-descr {
            text-align: center;
        }

        .srm-pages {
            min-width: 0;
        }

        .sr-listing {
            min-width: 0;
        }

        .sr-listing-photos {
            max-width: 330px;
            width: auto;
        }
    }

    @media print {
        body {
            background: none !important;
            color: #000;
            font-size: 10pt;
        }

        footer a[rel=bookmark]:link:after,
        footer a[rel=bookmark]:visited:after {
            content: " [" attr(href) "] "; /* Show URLs */
        }

        a {
            text-decoration: none;
        }

        .entry-content img,
        .comment-content img,
        .author-avatar img,
        img.wp-post-image {
            border-radius: 0;
            box-shadow: none;
        }

        .site {
            clear: both !important;
            display: block !important;
            float: none !important;
            max-width: 100%;
            position: relative !important;
        }

        .site-header {
            margin-bottom: 72px;
            margin-bottom: 5.142857143rem;
            text-align: left;
        }

        .site-header h1 {
            font-size: 21pt;
            line-height: 1;
            text-align: left;
        }

        .site-header h2 {
            color: #000;
            font-size: 10pt;
            text-align: left;
        }

        .site-header h1 a,
        .site-header h2 a {
            color: #000;
        }

        .author-avatar,
        #colophon,
        #respond,
        .commentlist .comment-edit-link,
        .commentlist .reply,
        .entry-header .comments-link,
        .entry-meta .edit-link a,
        .page-link,
        .site-content nav,
        .widget-area,
        img.header-image,
        .main-navigation {
            display: none;
        }

        .wrapper {
            border-top: none;
            box-shadow: none;
        }

        .site-content {
            margin: 0;
            width: auto;
        }

        .singular .entry-header .entry-meta {
            position: static;
        }

        .singular .site-content,
        .singular .entry-header,
        .singular .entry-content,
        .singular footer.entry-meta,
        .singular .comments-title {
            margin: 0;
            width: 100%;
        }

        .entry-header .entry-title,
        .entry-title,
        .singular .entry-title {
            font-size: 21pt;
        }

        footer.entry-meta,
        footer.entry-meta a {
            color: #444;
            font-size: 10pt;
        }

        .author-description {
            float: none;
            width: auto;
        }

        /* Comments */
        .commentlist > li.comment {
            background: none;
            position: relative;
            width: auto;
        }

        .commentlist .avatar {
            height: 39px;
            left: 2.2em;
            top: 2.2em;
            width: 39px;
        }

        .comments-area article header cite,
        .comments-area article header time {
            margin-left: 50px;
            margin-left: 3.57142857rem;
        }
    }

    .sr-listing {
        margin: 10px 3%;
        float: left;
        background-color: #ebebeb;
        margin: 20px 6px;
        padding: 10px 1% 1px;
        border-radius: 5px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        -moz-box-shadow: 3px 3px 2px #BABABA;
        -webkit-box-shadow: 3px 3px 2px #BABABA;
        box-shadow: 3px 3px 2px #BABABA;
        position: relative;
        width: 190px;
        height: 345px;
    }

    .sr-listing .sr-listing-header img {
        width: 190px;
        height: 120px;
    }

    #content h3.sr-listing-title {
        font-size: 17px;
        min-height: 65px;
        text-align: center;
        margin: 0;
    }

    .srl-region {
        color: #888888;
        font: 12px/16px 'swiss721BT';
        text-align: center;
    }

    .srl-price {
        color: #cc0000;
        font: 16px/35px 'swis721_btbold';
        text-align: center;
        display: block;
    }

    .srl-subdivision {
        color: #666666;
        font: 12px/28px 'swiss721BT';
        text-align: center;
        display: block;
    }

    #content .sr-listing hr {
        margin-bottom: 0;
    }

    .srl-hr {
        background-color: #ababab;
        border: 0;
        height: 1px;
        width: 90%;
        margin: 0 auto;
    }

    .sr-listing-body .details {
        float: left;
        width: 100%;
        text-align: center;
        padding: 8px 0;
    }

    .sr-listing-body .details li {
        border-radius: 0px;
        -webkit-border-radius: 0px;
        -moz-border-radius: 0px;
        -moz-box-shadow: none;
        -webkit-box-shadow: none;
        box-shadow: none;
        background-position: 0px 50%;
        display: inline;
        float: none;
        height: auto;
        line-height: 18px;
        font-size: 14px;
        margin: 0;
        padding-left: 20px;
        padding-right: 6%;
    }

    .sr-listing-body .details li.bath {
        background: url(%WP_PLUGIN_URL%/seo-rets/resources/images/baths.png) no-repeat 0 center;
    }

    .sr-listing-body .details li.bed {
        background: url(%WP_PLUGIN_URL%/seo-rets/resources/images/beds.png) no-repeat 0 center;
    }

    li.area {
        background: url(%WP_PLUGIN_URL%/seo-rets/resources/images/area.png) no-repeat 0 center;
        padding: 0 6% 0 16px;
    }

    .srl-mls_id {
        color: #666666;
        font: 11px/22px 'swiss721BT';
        text-align: center;
        display: block;
    }

    .backButton {
        width: 20px;
    }
</style>