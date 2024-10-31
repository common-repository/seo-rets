<?php
wp_enqueue_style('sr_templates_list', $this->css_resources_dir . 'templates/list.css');
wp_print_styles(array('sr_templates_list'));

$prefix = $type . '_' . $object;
?>
<div class="<?php echo $prefix; ?> sr-list-main">

    <?php

    $alphas = range('A', 'Z');

    if ($object == "city") {
        $link = "/sr-cities/";
    } elseif ($object == "subdivision") {
        $link = "/sr-communities/";
    } elseif (($type == 'cnd') || ($object == 'proj_name')) {
        $link = "/sr-condos/";
    } else {
        $link = 'die';
    }
    ?>

    <?php

    $lettersAmount = array();
    foreach ($alphas as $alpha) {
        ?><a class="<?php echo $alpha ?> sr_alpha"
             href="#<?php echo $prefix . '_' . $alpha ?>"><?php echo $alpha ?></a><?php
        $lettersAmount[$alpha] = 0;
    }

    $perPage = 50;
    $perColumn = 25;
    $z = 0;
    $count = count($request->result);

    $columns = array();


    foreach ($request->result as $key => $element) {
        $element = trim($element);
        preg_match('/^[^A-Za-z\s]+/', $element, $matches, PREG_OFFSET_CAPTURE);
        if (!empty($matches)) {
            $aLenHalf = round(strlen($request->result[$z]) / 2);
            preg_match('/\s/', $element, $matches2, PREG_OFFSET_CAPTURE);
            if (empty($matches2) || ($matches2[0][1] > $aLenHalf)) {
                $secondName = preg_replace('/^[^A-Za-z\s]+/', '', $element);
            } else {
                $secondName = preg_replace('/^[^A-Za-z\s]+.*?\s/', '', $element);
            }
        } else {
            $secondName = $element;
        }
        $secondName = ucfirst($secondName);
        if (empty($secondName[0]) || $secondName[0] == '') {
            echo '1' . $element . '1';
        }

        $lettersAmount[$secondName[0]]++;
    }
    foreach ($lettersAmount as $letter => $value) {
        $columnNumber = 1;
        $summ = 0;
        while ($value >= $perPage) {
            $summ += $perColumn;
            $columns[$letter][$columnNumber] = $summ;
            $columnNumber++;
            $summ += $perColumn;
            $columns[$letter][$columnNumber] = $summ;
            $columnNumber++;
            $value = $value - $perColumn - $perColumn;
        }
        if ($value > 1 && $value < $perPage) {
            $summ += ceil($value / 2);
            $columns[$letter][$columnNumber] = $summ;
            $columnNumber++;
            $summ += $value - ceil($value / 2);
            $columns[$letter][$columnNumber] = $summ;
        } elseif ($value == 1) {
            $summ++;
            $columns[$letter][$columnNumber] = $summ;
        }
    }

    ?>
    <div class="sr-List">
        <?php

        foreach ($alphas as $alpha) {
        $page = 2;
        $t = 0;
        $column = 1;
        //        echo '<div class="letter ' . $alpha . '"><a name="' . $prefix . '_' . $alpha . '"></a><div class="letterData' . $alpha . '">';
        //        echo '<div class="' . $alpha . '_1"><div class="listColumn"><ul>';
        ?>
        <div class="letter '<?php echo $alpha ?>"><a name="<?php echo $prefix . '_' . $alpha ?>"></a>
            <div class="letterData <?php echo $alpha ?> ">
                <div class="<?php echo $alpha ?>_1">
                    <div class="listColumn">
                        <ul>
                            <?

                            preg_match('/^[^A-Za-z\s]+/', $request->result[$z], $matches, PREG_OFFSET_CAPTURE);
                            if (!empty($matches)) {
                                $aLenHalf = round(strlen($request->result[$z]) / 2);
                                preg_match('/\s/', $request->result[$z], $matches2, PREG_OFFSET_CAPTURE);
                                if (empty($matches2) || ($matches2[0][1] > $aLenHalf)) {
                                    $secondName = preg_replace('/^[^A-Za-z\s]+/', '', $request->result[$z]);
                                } else {
                                    $secondName = preg_replace('/^[^A-Za-z\s]+.*?\s/', '', $request->result[$z]);
                                }
                            } else {
                                $secondName = $request->result[$z];
                            }
                            $secondName = ucfirst($secondName);


                            while ((empty($secondName) || !preg_match('/[A-Z]/', $secondName)) && ($z < $request->count)) {
                                $z++;
                            }
                            while (($alpha === $secondName[0]) && ($z < $request->count)) {
                            while ((empty($secondName) || !preg_match('/[A-Z]/', $secondName)) && ($z < $request->count)) {
                                $z++;
                            }

                            $currObjectLink = get_bloginfo("url") . $link . preg_replace('/\s/', '+', $request->result[$z]) . '/' . $type;
                            ?>
                            <li class="li"><span class="SRA_element"><a
                                        href="<?php echo $currObjectLink ?>"><?php echo $secondName ?></a></span>
                            </li>
                            <?php
                            $z++;
                            $t++;
                            if ($t % $perPage == 0) {
                            ?>
                        </ul>
                    </div>
                </div>
                <?php
                if ($t != end($columns[$alpha])) {
                ?>
                <div class="<?php echo $alpha . '_' . $page ?>" style="display:none">
                    <div class="listColumn">
                        <ul>
                            <?php
                            } else {
                                $flag1 = true;
                                break;
                            }
                            $column++;
                            $page++;
                            }
                            if ($t == $columns[$alpha][$column]) {
                            ?>
                        </ul>
                    </div>

                    <div class="listColumn">
                        <ul>
                            <?php
                            $column++;
                            }

                            preg_match('/^[^A-Za-z\s]+/', $request->result[$z], $matches, PREG_OFFSET_CAPTURE);
                            if (!empty($matches)) {
                                $aLenHalf = round(strlen($request->result[$z]) / 2);
                                preg_match('/\s/', $request->result[$z], $matches2, PREG_OFFSET_CAPTURE);
                                if (empty($matches2) || ($matches2[0][1] > $aLenHalf)) {
                                    $secondName = preg_replace('/^[^A-Za-z\s]+/', '', $request->result[$z]);
                                } else {
                                    $secondName = preg_replace('/^[^A-Za-z\s]+.*?\s/', '', $request->result[$z]);
                                }
                            } else {
                                $secondName = $request->result[$z];
                            }
                            $secondName = ucfirst($secondName);
                            }

                            if (isset($flag1) && $flag1) {
                            //div was here
                            ?>
                        </ul>
                        <?php
                        $flag1 = false;
                        } else {
                        ?>
                    </div>

                </div>
            </div>

            <?php
            }

            if ($z == $request->count) {
            //                    echo "break";
            ?>
            <div class="clear"></div>
        </div>
    <?php
    break;

    }
    ?>
        <div style="clear:both"></div>
        <?php
        if ($page > 2 && $lettersAmount[$alpha] != $perPage) {
            $page--;
            ?>
            <div class="<?php echo $alpha ?>">
                <?php
                for ($i = 1; $i <= $page; $i++) {
                    ?><a class="page pageNumber pageNumber_'<?php echo $i ?>"><?php echo $i ?></a> |<?php
                }
                ?>
            </div>
            <?php
        }
        ?>
        <div class="clear"></div>
    </div>
<?php
}
?>
    <div class="clear"></div>
    <!--</div>-->


    <script type="text/javascript">
        if (typeof window.defaultAlphaID == 'undefined') {
            var defaultAlphaID = Array();
        }
        window.defaultAlphaID.<?php echo $prefix;?> = 'A';
        jQuery(document).ready(function () {
            jQuery('.<?php echo $prefix;?> .page').click(function () {
                var prefix = "<?php echo $prefix;?>";
                var part2 = jQuery(this).text();
                var part1 = jQuery(this).parent().attr("class");
                jQuery(' .' + prefix + ' .letterData' + part1 + ' div').hide();
                jQuery(' .' + prefix + ' .' + part1 + '_' + part2).show();
                jQuery(' .' + prefix + ' .' + part1 + '_' + part2 + ' div').show();
            });


        });
    </script>
</div>
</div>
