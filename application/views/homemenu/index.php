<div class="row">
    <div class="col-md-12">
        <?php
        $this->load->helper('form');
        $error = $this->session->flashdata('error');
        if ($error) {
            ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php } ?>
        <?php
        $success = $this->session->flashdata('success');
        if ($success) {
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

<form  id="form1" name="form1" method="post">
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
        <div class="row"  style ="max-width:800px;">
        <div class="col-md-12">
            <div class="white-box form-horizontal">

                <div class="table-wrap">
                    <table class="table table-striped" >
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>タイトル</th>
                            <th width="180">使用可能</th>
                            <th width="90">順序</th>
                            <th width="60"></th>
                            <th width="60"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i=1;
                        if(!empty($menus))
                        {
                            foreach($menus as $record)
                            {
                                ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $record['menu_name']; ?></td>
                                    <td>
                                        <select class="form-control" onchange="
                                                $('#menu_id').val(<?php echo $record['id']; ?>);
                                                $('#is_use').val(this.value);
                                                $('#mode').val('save');
                                                form.submit();
                                                ">
                                            <option value="0" <?php if($record['is_use']==0){ echo 'selected';} ?>>使用しない</option>
                                            <option value="1" <?php if($record['is_use']==1){ echo 'selected';} ?>>使用する</option>
                                            <option value="2" <?php if($record['is_use']==2){ echo 'selected';} ?>>ユーザー限定</option>
                                        </select>
                                    </td>
                                    <td><?php echo $record['sort']; ?></td>
                                    <td>
                                        <button class="btn" onclick="
                                                $('#mode').val('up');
                                                $('#menu_id').val(<?php echo $record['id']; ?>);
                                                form.submit();
                                                "><i class="fa fa-arrow-up"></i> </button>
                                    </td>
                                    <td>
                                        <button class="btn" onclick="
                                                $('#mode').val('down');
                                                $('#menu_id').val(<?php echo $record['id']; ?>);
                                                form.submit();
                                                "><i class="fa fa-arrow-down"></i> </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="mode" id="mode" />
                <input type="hidden" name="menu_id" id="menu_id" />
                <input type="hidden" name="is_use" id="is_use" />

            </div>
        </div>
    </div>
    </div>
</form>
