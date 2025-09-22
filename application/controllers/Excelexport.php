<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/core/UpdateController.php';

class Excelexport extends UpdateController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct(ROLE_STAFF);

        $this->load->model('setting_model');
        $this->load->model('staff_model');
        $this->load->model('organ_model');

        $this->load->model('history_table_model');
        $this->load->model('history_table_menu_model');
        $this->load->model('history_table_menu_ticket_model');

        $this->load->model('staff_point_setting_model');
        $this->load->model('cart_model');
//        $this->load->model('staff_organ_model');

        $this->header['page'] = 'application';
        $this->header['sub_page'] = 'excel_export';
        $this->header['title'] = 'Excelエスポート';

        $this->load->library('excel');
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
//        $date_year = $this->input->post('date_year');
//        $date_month = $this->input->post('date_month');
//        $organ_id = $this->input->post('organ_id');
//
//
//        $organ_id = empty($organ_id) ? '' : $organ_id;
//        $date_year = empty($date_year) ? date('Y') : $date_year;
//        $date_month = empty($date_month) ? date('m') : $date_month;
//
//
//        $this->data['organ_list'] = $organ_list;
//        $this->data['date_year'] = $date_year;
//        $this->data['date_month'] = $date_month;
//        $this->data['organ_id'] = $organ_id;

        $this->load_view_with_menu("excelexport/index");
    }

    public function export1(){
        $date_year = $this->input->post('date_year');
        $date_month = $this->input->post('date_month');

        $date_year = empty($date_year) ? date('Y') : $date_year;
        $date_month = empty($date_month) ? date('m') : $date_month;

        $mod = $this->input->post('mod');

        if ($mod == 'export'){

            $company_id=2;

            $file_name = 'りらくーかん金山店予算_'.$date_year.'.'.$date_month.'_'.date('YmdHis').'.xls';
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setTitle('入力');
            $objPHPExcel->getActiveSheet()->freezePane('D4');

            $objPHPExcel->getActiveSheet()->getRowDimension(2)->setVisible(false);

            $objPHPExcel->getActiveSheet()->setShowGridlines(true);
            $border_thin = array(
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );

            $objPHPExcel->getActiveSheet()->getStyle('D4:AI34')->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'F7F5AD')
                    )
                )
            );


            $objPHPExcel->getActiveSheet()->getStyle('B1:E1')->getFont()->setSize(24);
            $objPHPExcel->getActiveSheet()->getStyle('B4:AI34')->getFont()->setSize(12);

            $objPHPExcel->getActiveSheet()
                ->getStyle('E4:AH35')
                ->getNumberFormat()
                ->setFormatCode('#,##0');


            for($i=1;$i<=34;$i++){
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($i, 3)->applyFromArray($border_thin);
            }


            $objPHPExcel->getActiveSheet()->SetCellValue('P1', '利用');
            $objPHPExcel->getActiveSheet()->SetCellValue('AB1', '販売');

            $objPHPExcel->getActiveSheet()->SetCellValue('B3', '日付');
            $objPHPExcel->getActiveSheet()->SetCellValue('C3', '曜日');
            $objPHPExcel->getActiveSheet()->SetCellValue('D3', '天候');
            $objPHPExcel->getActiveSheet()->SetCellValue('E3', 'PM施術売上');
            $objPHPExcel->getActiveSheet()->SetCellValue('F3', 'PM物販売上');
            $objPHPExcel->getActiveSheet()->SetCellValue('G3', 'クレジット');
            $objPHPExcel->getActiveSheet()->SetCellValue('H3', '電子マネー');
            $objPHPExcel->getActiveSheet()->SetCellValue('I3', 'お断り');
            $objPHPExcel->getActiveSheet()->SetCellValue('J3', '来店客数');
            $objPHPExcel->getActiveSheet()->SetCellValue('K3', '施術値引');
            $objPHPExcel->getActiveSheet()->SetCellValue('L3', '回数券値引');
            $objPHPExcel->getActiveSheet()->SetCellValue('M3', '10分無料');
            $objPHPExcel->getActiveSheet()->SetCellValue('N3', 'HPB');
            $objPHPExcel->getActiveSheet()->SetCellValue('O3', 'ギフトカード');
            $objPHPExcel->getActiveSheet()->SetCellValue('P3', '5回利用');
            $objPHPExcel->getActiveSheet()->SetCellValue('Q3', '11回利用');
            $objPHPExcel->getActiveSheet()->SetCellValue('R3', '朝割時間内');
            $objPHPExcel->getActiveSheet()->SetCellValue('S3', '朝割時間外');
            $objPHPExcel->getActiveSheet()->SetCellValue('T3', '90分利用');
            $objPHPExcel->getActiveSheet()->SetCellValue('U3', '周年回数券');
            $objPHPExcel->getActiveSheet()->SetCellValue('V3', 'ＰＡ');
            $objPHPExcel->getActiveSheet()->SetCellValue('W3', '研修ＰＡ');
            $objPHPExcel->getActiveSheet()->SetCellValue('X3', '店長');
            $objPHPExcel->getActiveSheet()->SetCellValue('Y3', 'MG');
            $objPHPExcel->getActiveSheet()->SetCellValue('Z3', '誤差＋');
            $objPHPExcel->getActiveSheet()->SetCellValue('AA3', '誤差－');
            $objPHPExcel->getActiveSheet()->SetCellValue('AB3', '5回');
            $objPHPExcel->getActiveSheet()->SetCellValue('AC3', '11回');
            $objPHPExcel->getActiveSheet()->SetCellValue('AD3', '朝回');
            $objPHPExcel->getActiveSheet()->SetCellValue('AE3', '90分券');
            $objPHPExcel->getActiveSheet()->SetCellValue('AF3', '周年回数券');
            $objPHPExcel->getActiveSheet()->SetCellValue('AG3', 'ケア分数');
            $objPHPExcel->getActiveSheet()->SetCellValue('AH3', '小口入金額');
            $objPHPExcel->getActiveSheet()->SetCellValue('AI3', 'コメント');


            $first_date = new DateTime($date_year.'-'.$date_month.'-1');
            $diff1Month = new DateInterval('P1M');
            $diff1Day = new DateInterval('P1D');
            $first_date->add($diff1Month)->sub($diff1Day);
            $to_row = $first_date->format('d');

            $_date = new DateTime($date_year.'-'.$date_month.'-1');

            $objPHPExcel->getActiveSheet()->mergeCells("B1:E1");
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', $_date->format('Y年n月'));


            $PAList = $this->staff_point_setting_model->getPAList($company_id, 1, $_date->format('Y-m'));
            $NPAList = $this->staff_point_setting_model->getPAList($company_id, 2, $_date->format('Y-m'));

            $days=['', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

            for ($i=1;$i<=$to_row;$i++){

                for($ii=1;$ii<=34;$ii++){
                    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($ii, $i+3)->applyFromArray($border_thin);
                }

                $cur_date = $_date->format('Y-m-d');

                $row = $i+3;
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $i);
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $days[$_date->format('N')]);


                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $this->history_table_menu_model->getMenuSumAmount($cur_date, '1'));//coupon, ticket using later

                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$row, $this->history_table_model->getSumAmountOneDay($cur_date, '3'));
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$row, $this->history_table_model->getSumAmountOneDay($cur_date, '2'));
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.$row, $this->history_table_model->getRejectCount($cur_date, '1'));
                $objPHPExcel->getActiveSheet()->SetCellValue('J'.$row, $this->history_table_model->getRejectCount($cur_date, '0'));
                $objPHPExcel->getActiveSheet()->SetCellValue('K'.$row, $this->history_table_model->getOrderMinusAmount($cur_date));
                $objPHPExcel->getActiveSheet()->SetCellValue('L'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date], 1));
                $objPHPExcel->getActiveSheet()->SetCellValue('N'.$row, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('O'.$row, '0');
                $objPHPExcel->getActiveSheet()->SetCellValue('P'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date, 'ticket_master_id'=>1]));
                $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date, 'ticket_master_id'=>2]));
                $objPHPExcel->getActiveSheet()->SetCellValue('R'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date, 'ticket_master_id'=>3, 'ticket_use_am'=>1]));
                $objPHPExcel->getActiveSheet()->SetCellValue('S'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date, 'ticket_master_id'=>3, 'ticket_use_pm'=>1]));
                $objPHPExcel->getActiveSheet()->SetCellValue('T'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date, 'ticket_master_id'=>4]));
                $objPHPExcel->getActiveSheet()->SetCellValue('U'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date, 'ticket_master_id'=>5]));
                $objPHPExcel->getActiveSheet()->SetCellValue('V'.$row, $this->getPermissionAttendanceTime($cur_date, $company_id,1, $PAList));//attendance later
                $objPHPExcel->getActiveSheet()->SetCellValue('W'.$row, $this->getPermissionAttendanceTime($cur_date, $company_id,1, $NPAList));//attendance later
                $objPHPExcel->getActiveSheet()->SetCellValue('X'.$row, $this->getPermissionAttendanceTime($cur_date, $company_id,2));//attendance later
                $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$row, $this->getPermissionAttendanceTime($cur_date, $company_id,3));//attendance later

                $objPHPExcel->getActiveSheet()->SetCellValue('AB'.$row, $this->cart_model->getTicketSaleCount(['sale_date'=>$cur_date, 'ticket_master_id'=>1, 'company_id'=>$company_id] ));
                $objPHPExcel->getActiveSheet()->SetCellValue('AC'.$row, $this->cart_model->getTicketSaleCount(['sale_date'=>$cur_date, 'ticket_master_id'=>2, 'company_id'=>$company_id] ));
                $objPHPExcel->getActiveSheet()->SetCellValue('AD'.$row, $this->cart_model->getTicketSaleCount(['sale_date'=>$cur_date, 'ticket_master_id'=>3, 'company_id'=>$company_id] ));
                $objPHPExcel->getActiveSheet()->SetCellValue('AE'.$row, $this->cart_model->getTicketSaleCount(['sale_date'=>$cur_date, 'ticket_master_id'=>4, 'company_id'=>$company_id] ));
                $objPHPExcel->getActiveSheet()->SetCellValue('AF'.$row, $this->cart_model->getTicketSaleCount(['sale_date'=>$cur_date, 'ticket_master_id'=>5, 'company_id'=>$company_id] ));

                $_date->add($diff1Day);
            }


            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$file_name.'');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');

//            $objWriter = new PHPExcel_Writer_HTML($objPHPExcel);
//
//            header('Content-Type: application/vnd.ms-excel');
//            header('Content-Disposition: attachment;filename="'.$file_name.'"');
//            $objWriter->save('php://output');
//            die('s');
        }


        $this->data['date_year'] = $date_year;
        $this->data['date_month'] = $date_month;


        $this->load_view_with_menu("excelexport/export1");
    }

    public function export2(){
        $date_year = $this->input->post('date_year');
        $date_month = $this->input->post('date_month');

        $date_year = empty($date_year) ? date('Y') : $date_year;
        $date_month = empty($date_month) ? date('m') : $date_month;

        $mod = $this->input->post('mod');

        if ($mod == 'export'){

            $company_id=2;

            $file_name = '手温イオンモール木曽川店予算_'.$date_year.'.'.$date_month.'_'.date('YmdHis').'.xls';
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setTitle('入力');
            $objPHPExcel->getActiveSheet()->freezePane('D4');

            $objPHPExcel->getActiveSheet()->getRowDimension(2)->setVisible(false);

            $objPHPExcel->getActiveSheet()->setShowGridlines(true);
            $border_thin = array(
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );

//            $objPHPExcel->getActiveSheet()->getStyle('D4:AI34')->applyFromArray(
//                array(
//                    'fill' => array(
//                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                        'color' => array('rgb' => 'F7F5AD')
//                    )
//                )
//            );


            $objPHPExcel->getActiveSheet()->getStyle('B1:E1')->getFont()->setSize(24);
            $objPHPExcel->getActiveSheet()->getStyle('B4:AI37')->getFont()->setSize(12);

            $objPHPExcel->getActiveSheet()
                ->getStyle('E4:AH35')
                ->getNumberFormat()
                ->setFormatCode('#,##0');


            for($i=1;$i<=34;$i++){
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($i, 3)->applyFromArray($border_thin);
            }


            $objPHPExcel->getActiveSheet()->SetCellValue('P1', '利用');
            $objPHPExcel->getActiveSheet()->SetCellValue('AB1', '販売');

            $objPHPExcel->getActiveSheet()->SetCellValue('B3', '日付');
            $objPHPExcel->getActiveSheet()->SetCellValue('C3', '曜日');
            $objPHPExcel->getActiveSheet()->SetCellValue('D3', '天候');
            $objPHPExcel->getActiveSheet()->SetCellValue('E3', 'PM施術売上');
            $objPHPExcel->getActiveSheet()->SetCellValue('F3', 'PM物販売上');
            $objPHPExcel->getActiveSheet()->SetCellValue('G3', 'クレジット');
            $objPHPExcel->getActiveSheet()->SetCellValue('H3', '電子マネー');
            $objPHPExcel->getActiveSheet()->SetCellValue('I3', 'お断り');
            $objPHPExcel->getActiveSheet()->SetCellValue('J3', '来店客数');
            $objPHPExcel->getActiveSheet()->SetCellValue('K3', '施術値引');
            $objPHPExcel->getActiveSheet()->SetCellValue('L3', '回数券値引');
            $objPHPExcel->getActiveSheet()->SetCellValue('M3', '10分無料');
            $objPHPExcel->getActiveSheet()->SetCellValue('N3', 'HPB');
            $objPHPExcel->getActiveSheet()->SetCellValue('O3', 'ギフトカード');
            $objPHPExcel->getActiveSheet()->SetCellValue('P3', '20分券回収');
            $objPHPExcel->getActiveSheet()->SetCellValue('Q3', '10分券配布');
            $objPHPExcel->getActiveSheet()->SetCellValue('R3', '10分券回収');
            $objPHPExcel->getActiveSheet()->SetCellValue('S3', 'ハピチケ');
            $objPHPExcel->getActiveSheet()->SetCellValue('T3', '極手温3回');
            $objPHPExcel->getActiveSheet()->SetCellValue('U3', '極手温5回');
            $objPHPExcel->getActiveSheet()->SetCellValue('V3', '極手温10回');
            $objPHPExcel->getActiveSheet()->SetCellValue('W3', '指名券回収');
            $objPHPExcel->getActiveSheet()->SetCellValue('X3', 'ＰＡ');
            $objPHPExcel->getActiveSheet()->SetCellValue('Y3', '研修ＰＡ');
            $objPHPExcel->getActiveSheet()->SetCellValue('Z3', '店長');
            $objPHPExcel->getActiveSheet()->SetCellValue('AA3', 'MG');
            $objPHPExcel->getActiveSheet()->SetCellValue('AB3', '誤差＋');
            $objPHPExcel->getActiveSheet()->SetCellValue('AC3', '誤差－');
            $objPHPExcel->getActiveSheet()->SetCellValue('AD3', '20分回数券');
            $objPHPExcel->getActiveSheet()->SetCellValue('AE3', '極手温3回');
            $objPHPExcel->getActiveSheet()->SetCellValue('AF3', '極手温5回');
            $objPHPExcel->getActiveSheet()->SetCellValue('AG3', '極手温10回');
            $objPHPExcel->getActiveSheet()->SetCellValue('AH3', '極手温10回');
            $objPHPExcel->getActiveSheet()->SetCellValue('AI3', 'ケア分数');
            $objPHPExcel->getActiveSheet()->SetCellValue('AJ3', 'ハピチケ');
            $objPHPExcel->getActiveSheet()->SetCellValue('AK3', '会議MT業務');
            $objPHPExcel->getActiveSheet()->SetCellValue('AL3', 'コメント');


            $first_date = new DateTime($date_year.'-'.$date_month.'-1');
            $diff1Month = new DateInterval('P1M');
            $diff1Day = new DateInterval('P1D');
            $first_date->add($diff1Month)->sub($diff1Day);
            $to_row = $first_date->format('d');

            $_date = new DateTime($date_year.'-'.$date_month.'-1');

            $objPHPExcel->getActiveSheet()->mergeCells("B1:E1");
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', $_date->format('Y年n月'));


            $PAList = $this->staff_point_setting_model->getPAList($company_id, 1, $_date->format('Y-m'));
            $NPAList = $this->staff_point_setting_model->getPAList($company_id, 2, $_date->format('Y-m'));

            $days=['', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

            for ($i=1;$i<=$to_row;$i++){

                $cur_date = $_date->format('Y-m-d');
                if (date('Y-m-d')<$cur_date) break;

                for($ii=1;$ii<=37;$ii++){
                    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($ii, $i+3)->applyFromArray($border_thin);
                }


                $row = $i+3;
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row, $i);
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$row, $days[$_date->format('N')]);


//                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row, $this->history_table_menu_model->getMenuSumAmount($cur_date, '1'));//coupon, ticket using later
//
//                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$row, $this->history_table_model->getSumAmountOneDay($cur_date, '3'));
//                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$row, $this->history_table_model->getSumAmountOneDay($cur_date, '2'));
//                $objPHPExcel->getActiveSheet()->SetCellValue('I'.$row, $this->history_table_model->getRejectCount($cur_date, '1'));
//                $objPHPExcel->getActiveSheet()->SetCellValue('J'.$row, $this->history_table_model->getRejectCount($cur_date, '0'));
//                $objPHPExcel->getActiveSheet()->SetCellValue('K'.$row, $this->history_table_model->getOrderMinusAmount($cur_date));
//                $objPHPExcel->getActiveSheet()->SetCellValue('L'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date], 1));
//                $objPHPExcel->getActiveSheet()->SetCellValue('N'.$row, '');
//                $objPHPExcel->getActiveSheet()->SetCellValue('O'.$row, '0');
//                $objPHPExcel->getActiveSheet()->SetCellValue('P'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date, 'ticket_master_id'=>1]));
//                $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date, 'ticket_master_id'=>2]));
//                $objPHPExcel->getActiveSheet()->SetCellValue('R'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date, 'ticket_master_id'=>3, 'ticket_use_am'=>1]));
//                $objPHPExcel->getActiveSheet()->SetCellValue('S'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date, 'ticket_master_id'=>3, 'ticket_use_pm'=>1]));
//                $objPHPExcel->getActiveSheet()->SetCellValue('T'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date, 'ticket_master_id'=>4]));
//                $objPHPExcel->getActiveSheet()->SetCellValue('U'.$row, $this->history_table_menu_ticket_model->getTicketCount(['sel_date'=>$cur_date, 'ticket_master_id'=>5]));
                $objPHPExcel->getActiveSheet()->SetCellValue('X'.$row, $this->getPermissionAttendanceTime($cur_date, $company_id,1, $PAList));//attendance later
                $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$row, $this->getPermissionAttendanceTime($cur_date, $company_id,1, $NPAList));//attendance later
                $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$row, $this->getPermissionAttendanceTime($cur_date, $company_id,2));//attendance later
                $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$row, $this->getPermissionAttendanceTime($cur_date, $company_id,3));//attendance later

                $_date->add($diff1Day);
            }

            $objPHPExcel->getActiveSheet()->getStyle('D4:AL'.($i+2))->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'F7F5AD')
                    )
                )
            );

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$file_name.'');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');

        }


        $this->data['date_year'] = $date_year;
        $this->data['date_month'] = $date_month;


        $this->load_view_with_menu("excelexport/export2");
    }

    private function getPermissionAttendanceTime($date, $company_id, $auth, $paList=''){

        if ($auth==1 && $paList=='') return 0;

        $this->load->model('shift_model');
        $this->load->model('attendance_model');

        $shifts = $this->shift_model->getShiftTimeListByAuth($date, $company_id, $auth, $paList);

        $sum_time = 0;
        foreach ($shifts as $shift){
            $sum_time += $shift['shift_time'];

        }
        return $sum_time;
    }

}

?>
