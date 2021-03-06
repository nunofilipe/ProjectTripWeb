<?php

use yii\helpers\Html;

?>

<html lang="<?= Yii::$app->language ?>">

<h1><b>Top</b></h1>

<div class="container">

    <div class="col-lg-5">
        <h2>Top Visited</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Position</th>
                <th>Country</th>
                <th>Number of visits</th>
            </tr>
            </thead>
            <tbody>

        <?php
        $x=0; ?><?php
            foreach ($topvisited as $row) { ?>
                <tr>
               <?php $x++; ?>
                    <td><?php echo $x ?></td>
                    <td><?php echo Html::img("$row->flag", ['width' => '30px'])." ".$row->name ?></td>
                    <td><?php echo $row->numero ?></td>
            <?php } ?>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-lg-2"></div>
    <div class="col-lg-5">
        <h2>Top Rated </h2>
        <table class="table">
            <thead>
            <tr>
                <th>Position</th>
                <th>Country</th>
                <th>Average rating</th>
            </tr>
            </thead>
            <tbody>

            <?php
            $x=0; ?><?php
            foreach ($toprated as $row) { ?>
            <tr>
                <?php $x++; ?>
                <td><?php echo $x ?></td>
                <td><?php echo Html::img("$row->flag", ['width' => '30px'])." ".$row->name ?></td>
                <td><?php echo round($row->averagerating,2) ?></td>


                <?php } ?>
            </tr>
            </tbody>
        </table>
    </div>

</div>