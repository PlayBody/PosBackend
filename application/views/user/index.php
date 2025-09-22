<div class="row">
    <div class="col-md-12">
        <?php
        $this->load->helper('form');
        $error = $this->session->flashdata('error');
        if($error)
        {
            ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php } ?>
        <?php
        $success = $this->session->flashdata('success');
        if($success)
        {
            ?>
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-12">
                <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
            </div>
        </div>
    </div>
</div>

<form method="POST" id="searchList" method="post" action="<?php echo base_url(); ?>user/index">
    <div class="breadcome-area">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="breadcome-list">
                        <div class="row condition">
                            <div class="col-md-9 form-group-obj">
                                <span class="header-title">企業名 </span>

                                <select name="cond[company_id]" class="form-control" onchange="form.submit();">
                                    <?php foreach($companies as $item){ ?>
                                        <option value="<?php echo $item['company_id']; ?>" <?php if ($cond['company_id']== $item['company_id']) echo 'selected'; ?> >
                                            <?php echo $item['company_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box form-horizontal">
                    <div class="table-responsive">
                        <div id="example_wrapper" class="dataTables_wrapper">
                            <div id="example_filter" class="dataTables_filter">
                                <div class="input-group">
                                    <input type="text" name="cond[search]" value="<?php echo $cond['search']; ?>" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search"/>
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-default searchList"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                          <table id="example" class="display table dataTable" role="grid" aria-describedby="example_info">
                            <thead>
                            <tr role="row">
                                <th>No</th>
                                <th>ユーザーNo</th>
                                <th>ユーザー名</th>
                                <th>ニックネーム</th>
                                <th>メール</th>
                                <th>電話番号</th>
                                <th>性別</th>
                                <th>生年月日</th>
                                <th>登録日</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i=1;
                            if(!empty($users))
                            {
                                foreach($users as $record)
                                {
                                    ?>
                                    <tr role="row" class="<?php echo $i%2==0 ? 'odd' : 'even'; ?>">
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $record['user_no']; ?></td>
                                        <td><?php echo $record['user_first_name']. ' ' .$record['user_last_name']; ?></td>
                                        <td><?php echo $record['user_nick']; ?></td>
                                        <td><a href="<?php echo base_url(); ?>/user/edit?user_id=<?php echo $record['user_id']; ?>"><?php echo $record['user_email']; ?></td>
                                        <td><?php echo $record['user_tel']; ?></td>
                                        <td><?php echo $record['user_sex']==1 ? '男' : '女'; ?></td>
                                        <td><?php echo $record['user_birthday']; ?></td>
                                        <td><?php echo $record['create_date']; ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                            <div class="dataTables_paginate paging_simple_numbers" id="example_paginate">
                                <?php echo $this->pagination->create_links(); ?>
                            </div>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('ul.pagination li a').click(function (e) {
            e.preventDefault();
            var link = jQuery(this).get(0).href;
            var value = link.substring(link.lastIndexOf('/') + 1);
            jQuery("#searchList").attr("action", <?php echo base_url(); ?> + "userList/" + value);
            jQuery("#searchList").submit();
        });
    });
</script>
