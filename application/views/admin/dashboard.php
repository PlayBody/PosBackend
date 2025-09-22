<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-tachometer" aria-hidden="true"></i>ダッシュボード
            <small>コントロールパネル</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">


            <div class="col-lg-6 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <p>企業数</p>
                        <h3><?php echo $company_count; ?><span style="font-size: 2rem;">社.</span></h3>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="<?php echo admin_url(); ?>company" class="small-box-footer">もっと見る<i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->


            <div class="col-lg-6 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <p>登録されているシナリオ数</p>
                        <h3><?php echo $all_scenario_count; ?><span style="font-size: 2rem;">件</span></h3>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="<?php echo admin_url(); ?> " class="small-box-footer">もっと見る <i
                                class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->

            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-blue">
                    <div class="inner">
                        <p>セッション数</p>
                        <h3><?php echo $visit_count; ?><span style="font-size: 2rem;">人</span></h3>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <!--                    <a href="-->
                    <?php //echo base_url(); ?><!--scenario" class="small-box-footer">もっと見る <i class="fa fa-arrow-circle-right"></i></a>-->
                </div>
            </div><!-- ./col -->

            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <p>シナリオ選択数</p>
                        <h3><?php echo $scenario_count; ?><span style="font-size: 2rem;">人</span></h3>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <!--                    <a href="-->
                    <?php //echo base_url(); ?><!--scenario" class="small-box-footer">もっと見る <i class="fa fa-arrow-circle-right"></i></a>-->
                </div>
            </div><!-- ./col -->

            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-olive">
                    <div class="inner">
                        <p>FAQ選択数</p>
                        <h3><?php echo $faq_count; ?><span style="font-size: 2rem;">人</span></h3>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <!--                    <a href="-->
                    <?php //echo base_url(); ?><!--scenario" class="small-box-footer">もっと見る <i class="fa fa-arrow-circle-right"></i></a>-->
                </div>
            </div><!-- ./col -->

            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-maroon">
                    <div class="inner">
                        <p>チャット数</p>
                        <h3><?php echo $chat_count; ?><span style="font-size: 2rem;">人</span></h3>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <!--                    <a href="-->
                    <?php //echo base_url(); ?><!--scenario" class="small-box-footer">もっと見る <i class="fa fa-arrow-circle-right"></i></a>-->
                </div>
            </div><!-- ./col -->


        </div>
    </section>
</div>