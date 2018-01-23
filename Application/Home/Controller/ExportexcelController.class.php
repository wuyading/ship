<?php

namespace Home\Controller;


class ExportexcelController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->isLogin();
    }

    //    导出Excel表格

    public function export_excel()
    {
        $id_arr = I('get.i_select');
        if (empty($id_arr)) {
            $this->error('请选择下载内容！！！');
        }

        $where['id'] = array('in', $id_arr);

        $info = M('info')->where($where)->select();
        foreach ($info as $key => $value) {
            $info[$key]['extends'] = M('info_extend')->where('info_id=' . $value['id'])->select();
        }

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $count = array();
        foreach ($info as $k => $v) {
            $extends = count($v['extends']);
            $num = $k;
            $count[] = $extends;
            if ($num == 0) {
                $line = 1;
                $N = 3;
                $N4 = 4;
                $A5 = 5;
                $A6 = 6;
                $A7 = 7;
                $A9 = 9;
                $A10 = 10;
                $A11 = 11;
            } else {
                $line = 14 * $num + array_sum($count) - $extends;
                $N = $line + 2;
                $N4 = $line + 3;
                $A5 = $line + 4;
                $A6 = $line + 5;
                $A7 = $line + 6;
                $A9 = $line + 8;
                $A10 = $line + 9;
                $A11 = $line + 10;
            }

            $start = $A11;
            if ($extends == 0) {
                $end = $start;
            } else {
                $end = $start + $extends - 1;
            }

            $objActSheet = $objPHPExcel->getActiveSheet();

            //设置宽度以及自动换行
            $this->setAutoWrapText($objActSheet);

            $lines = $line + 1;
            $objActSheet->mergeCells('A' . $line . ':' . 'S' . $line);
            $objActSheet->mergeCells('A' . $lines . ':' . 'S' . $lines);
            $objActSheet->mergeCells('O' . $N . ':' . 'S' . $N);
            $objActSheet->mergeCells('B' . $A5 . ':' . 'D' . $A5);
            $objActSheet->mergeCells('H' . $A5 . ':' . 'J' . $A5);
            $objActSheet->mergeCells('O' . $A5 . ':' . 'S' . $A5);
            $objActSheet->mergeCells('B' . $A6 . ':' . 'D' . $A6);
            $objActSheet->mergeCells('H' . $A6 . ':' . 'J' . $A6);
            $objActSheet->mergeCells('N' . $A6 . ':' . 'O' . $A6);
            $objActSheet->mergeCells('P' . $A6 . ':' . 'S' . $A6);
            $objActSheet->mergeCells('A' . $A9 . ':' . 'A' . $A10);
            $objActSheet->mergeCells('B' . $A9 . ':' . 'B' . $A10);
            $objActSheet->mergeCells('C' . $A9 . ':' . 'C' . $A10);
            $objActSheet->mergeCells('D' . $A9 . ':' . 'D' . $A10);
            $objActSheet->mergeCells('E' . $A9 . ':' . 'E' . $A10);
            $objActSheet->mergeCells('F' . $A9 . ':' . 'F' . $A10);
            $objActSheet->mergeCells('G' . $A9 . ':' . 'G' . $A10);
            $objActSheet->mergeCells('H' . $A9 . ':' . 'H' . $A10);
            $objActSheet->mergeCells('I' . $A9 . ':' . 'I' . $A10);
            $objActSheet->mergeCells('J' . $A9 . ':' . 'J' . $A10);
            $objActSheet->mergeCells('K' . $A9 . ':' . 'K' . $A10);
            $objActSheet->mergeCells('L' . $A9 . ':' . 'L' . $A10);
            $objActSheet->mergeCells('M' . $A9 . ':' . 'M' . $A10);
            $objActSheet->mergeCells('N' . $A9 . ':' . 'N' . $A10);
            $objActSheet->mergeCells('O' . $A9 . ':' . 'S' . $A9);

            $objActSheet->mergeCells('B' . $A7 . ':' . 'M' . $A7);
            $objActSheet->mergeCells('N' . $A7 . ':' . 'O' . $A7);
            $objActSheet->mergeCells('P' . $A7 . ':' . 'S' . $A7);
            $objActSheet->mergeCells('A' . $start . ':' . 'A' . $end);
            $objActSheet->mergeCells('B' . $start . ':' . 'B' . $end);
            //Excel的第A列，uid是你查出数组的键值，下面以此类推
            $objActSheet->setCellValue('A' . $line, '潜在失效模式与影响分析（过程FMEA)');
            $objActSheet->setCellValue('A' . $lines, 'GLW00151503                                                                版本号：0');
            $objActSheet->setCellValue('N' . $N, 'FMEA编号');
            $objActSheet->setCellValue('N' . $N4, '共');
            $objActSheet->setCellValue('P' . $N4, '页');
            $objActSheet->setCellValue('Q' . $N4, '第');
            $objActSheet->setCellValue('S' . $N4, '页');
            $objActSheet->setCellValue('O' . $N, $v['h_no']);
            $objActSheet->setCellValue('O' . $N4, $v['h_total_pages']);
            $objActSheet->setCellValue('R' . $N4, $v['h_page_time']);
            $objActSheet->setCellValue('A' . $A5, '项目');
            $objActSheet->setCellValue('B' . $A5, $v['h_project']);
            $objActSheet->setCellValue('G' . $A5, '设计职责');
            $objActSheet->setCellValue('H' . $A5, $v['h_design']);
            $objActSheet->setCellValue('N' . $A5, '编制人');
            $objActSheet->setCellValue('O' . $A5, $v['h_edit_people']);
            $objActSheet->setCellValue('A' . $A6, '车型/年项目');
            $objActSheet->setCellValue('B' . $A6, $v['h_c_y']);
            $objActSheet->setCellValue('G' . $A6, '关键日期');
            $objActSheet->setCellValue('H' . $A6, trim($v['h_key_time'])?date("Y.m.d", $v['h_key_time']):'');
            $objActSheet->setCellValue('N' . $A6, 'FMEA日期（原始）');
            $objActSheet->setCellValue('N' . $A7, 'FMEA日期（更新）');
            $objActSheet->setCellValue('P' . $A6, trim($v['h_pfmea_start'])?date('Y.m.d', $v['h_pfmea_start']):'');
            $objActSheet->setCellValue('P' . $A7, trim($v['h_pfmea_end'])?date('Y.m.d', $v['h_pfmea_end']):'');
            $objActSheet->setCellValue('A' . $A7, '核心小组');
            $objActSheet->setCellValue('B' . $A7, $v['h_group']);
            $objActSheet->setCellValue('A' . $A9, '项目/功能');
            $objActSheet->setCellValue('B' . $A9, '要求');
            $objActSheet->setCellValue('C' . $A9, '潜在失效模式');
            $objActSheet->setCellValue('D' . $A9, '失效模式的潜在影响');
            $objActSheet->setCellValue('E' . $A9, '严重度');
            $objActSheet->setCellValue('F' . $A9, '分类');
            $objActSheet->setCellValue('G' . $A9, '失效潜在原因');
            $objActSheet->setCellValue('H' . $A9, '现行设计控制预防');
            $objActSheet->setCellValue('I' . $A9, '发生率');
            $objActSheet->setCellValue('J' . $A9, '现行设计控制探测');
            $objActSheet->setCellValue('K' . $A9, '探测度');
            $objActSheet->setCellValue('L' . $A9, 'RPN');
            $objActSheet->setCellValue('M' . $A9, '推荐措施');
            $objActSheet->setCellValue('N' . $A9, '职责&目标完成日期');
            $objActSheet->setCellValue('O' . $A9, '实际结果');
            $objActSheet->setCellValue('O' . $A10, '采取措施&有效日期');
            $objActSheet->setCellValue('P' . $A10, '严重度');
            $objActSheet->setCellValue('Q' . $A10, '发生度');
            $objActSheet->setCellValue('R' . $A10, '探测度');
            $objActSheet->setCellValue('S' . $A10, 'RPN');
            $objActSheet->setCellValue('A' . $A11, $v['project']);
            $objActSheet->setCellValue('B' . $A11, $v['require']);

            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRS' , $A9);
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRS' , $A10);

            if ($v['extends']) {
                foreach ($v['extends'] as $kk => $vv) {
                    $insert = $kk + $start;

                    $objActSheet->setCellValue('C' . $insert, $vv['mode']);
                    $objActSheet->setCellValue('D' . $insert, $vv['mode_effect']);
                    $objActSheet->setCellValue('E' . $insert, $vv['severity']);
                    $objActSheet->setCellValue('F' . $insert, $vv['category']);
                    $objActSheet->setCellValue('G' . $insert, $vv['cause']);
                    $objActSheet->setCellValue('H' . $insert, $vv['prevent']);
                    $objActSheet->setCellValue('I' . $insert, $vv['incidence']);
                    $objActSheet->setCellValue('J' . $insert, $vv['survey']);
                    $objActSheet->setCellValue('K' . $insert, $vv['detectivity']);
                    $objActSheet->setCellValue('L' . $insert, $vv['rpn']);
                    $objActSheet->setCellValue('M' . $insert, $vv['measures']);
                    $objActSheet->setCellValue('N' . $insert, trim($vv['over_time'])?date('Y.m.d', $vv['over_time']):'');
                    $objActSheet->setCellValue('O' . $insert, trim($vv['t_use_time'])?date('Y.m.d', $vv['t_use_time']):'');
                    $objActSheet->setCellValue('P' . $insert, $vv['t_severity']);
                    $objActSheet->setCellValue('Q' . $insert, $vv['t_incidence']);
                    $objActSheet->setCellValue('R' . $insert, $vv['t_detectivity']);
                    $objActSheet->setCellValue('S' . $insert, $vv['t_rpn']);

                    $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRS', $insert);
                }
            }

            //整体样式
            $objStyleB1 = $objActSheet->getStyle('A' . $lines . ':' . 'S' . $end);
            //设置字体
            $objFontB1 = $objStyleB1->getFont();
            $objFontB1->setName('宋体');
            $objFontB1->setSize(10);
            //$objFontB1->setBold(true);
            //$objFontA5->getColor()->setARGB('FF999999');
            //设置对齐方式
            $objAlignB1 = $objStyleB1->getAlignment();
            $objAlignB1->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objAlignB1->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //设置单元格宽
            $objActSheet->getColumnDimension('A')->setWidth(10);
            //设置行高
            $objActSheet->getRowDimension($line)->setRowHeight(30);
            $objActSheet->getRowDimension($lines)->setRowHeight(20);
            $objActSheet->getRowDimension($A9)->setRowHeight(20);
            $objActSheet->getRowDimension($A10)->setRowHeight(20);
            $objActSheet->getRowDimension($A11)->setRowHeight(20);
            //项目功能行加粗显示
            $objStyleProject = $objActSheet->getStyle('A' . $A9 . ':' . 'S' . $A10);
            $objFontProject = $objStyleProject->getFont();
            $objFontProject->setBold(true);
            //标题样式
            $objStyleA1 = $objActSheet->getStyle('A' . $line);
            //设置字体
            $objFontA1 = $objStyleA1->getFont();
            $objFontA1->setName('宋体');
            $objFontA1->setSize(14);
            $objFontA1->setBold(true);
            //        $objFontA5->getColor()->setARGB('FF999999');
            //设置对齐方式
            $objAlignA1 = $objStyleA1->getAlignment();
            $objAlignA1->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objAlignA1->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            //设置边框
            $this->setBorder($objActSheet,'OPQRS', $N,'Bottom');
            $this->setBorder($objActSheet,'OR', $N4,'Bottom');
            $this->setBorder($objActSheet,'BCDHIJOPQRS', $A5,'Bottom');
            $this->setBorder($objActSheet,'BCDHIJOPQRS', $A5,'Bottom');
            $this->setBorder($objActSheet,'BCDHIJPQRS', $A6,'Bottom');
            $this->setBorder($objActSheet,'BCDEFGHIJKLMPQRS', $A7,'Bottom');
        }

        $objActSheet->setTitle("PFMEA EXCEL");
        $filename = iconv("utf-8", "gbk", $v['project']);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename.'.xls');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');
        exit;
    }

    /**
     * @param $objActSheet  \PHPExcel_Worksheet
     * @param $str
     * @param $key
     * @param $type
     */
    private function setBorder($objActSheet,$str,$key,$type=''){
        $border = \PHPExcel_Style_Border::BORDER_THIN;

        for ($i=0;$i<strlen($str);$i++){
            $columName = $str[$i].$key;

            if(!empty($type)){
                if(is_array($type)){
                    foreach ($type as $row){
                        $func = 'get'.ucfirst($row);
                        $objActSheet->getStyle($columName)->getBorders()->$func()->setBorderStyle($border);
                    }
                }else{
                    $func = 'get'.ucfirst($type);
                    $objActSheet->getStyle($columName)->getBorders()->$func()->setBorderStyle($border);
                }
            }else{
                $objActSheet->getStyle($columName)->getBorders()->getTop()->setBorderStyle($border);
                $objActSheet->getStyle($columName)->getBorders()->getRight()->setBorderStyle($border);
                $objActSheet->getStyle($columName)->getBorders()->getBottom()->setBorderStyle($border);
                $objActSheet->getStyle($columName)->getBorders()->getLeft()->setBorderStyle($border);
            }
        }
    }

    /**
     * 设置宽度，以及自动换行
     *
     * @param $objActSheet
     */
    private function setAutoWrapText($objActSheet){
        $str='ABCDEFGHIJKLMNOPQRS';
        for ($i=0;$i<strlen($str);$i++){
            $objActSheet->getStyle($str[$i])->getAlignment()->setWrapText(true);
        }

        $str_arr = ['E','I','K','L','P','Q','R','S'];
        foreach ($str_arr as $row){
            $objActSheet->getColumnDimension($row)->setWidth(3.6);
        }
    }

    /**
     * 设置宽度，以及自动换行CEVT
     *
     * @param $objActSheet
     */
    private function setAutoWrapText_cevt($objActSheet){
        $str='ABCDEFGHIJKLMNOPQRSTUV';
        for ($i=0;$i<strlen($str);$i++){
            $objActSheet->getStyle($str[$i])->getAlignment()->setWrapText(true);
        }

        $str_arr = ['B','C','D','H','J','K','N','O','S','T','U','V'];
        foreach ($str_arr as $row){
            $objActSheet->getColumnDimension($row)->setWidth(2.6);
        }
    }


    //    导出Excel表格 CEVT

    public function export_excel_cevt()
    {
        $id_arr = I('get.i_select');
        if (empty($id_arr)) {
            $this->error('请选择下载内容！！！');
        }

        $where['id'] = array('in', $id_arr);

        $info = M('info_cevt')->where($where)->select();
        foreach ($info as $key => $value) {
            $info[$key]['extends'] = M('info_extend_cevt')->where('info_id=' . $value['id'])->select();
        }

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $count = array();
        foreach ($info as $k => $v) {
            $extends = count($v['extends']);
            $num = $k;
            $count[] = $extends;
            if ($num == 0) {
                $line = 1;
                $A2 = 2;
                $A3 = 3;
                $A4 = 4;
                $A5 = 5;
                $A6 = 6;
                $A7 = 7;
                $A8 = 8;
            } else {
                $line = 9 * $num + array_sum($count) - $extends;
                $A2 = $line+1;
                $A3 = $line+2;
                $A4 = $line+3;
                $A5 = $line + 4;
                $A6 = $line + 5;
                $A7 = $line + 6;
                $A8 = $line + 7;
            }

            $start = $A8;
            if ($extends == 0) {
                $end = $start;
            } else {
                $end = $start + $extends - 1;
            }

            $objActSheet = $objPHPExcel->getActiveSheet();

            //设置宽度以及自动换行
            $this->setAutoWrapText_cevt($objActSheet);
            $objActSheet->mergeCells('A' . $line . ':' . 'V' . $line);
            $objActSheet->mergeCells('A' . $A2 . ':' . 'F' . $A2);
            $objActSheet->mergeCells('G' . $A2 . ':' . 'G' . $A2);
            $objActSheet->mergeCells('H' . $A2 . ':' . 'I' . $A2);
            $objActSheet->mergeCells('J' . $A2 . ':' . 'O' . $A2);
            $objActSheet->mergeCells('P' . $A2 . ':' . 'Q' . $A2);
            $objActSheet->mergeCells('R' . $A2 . ':' . 'R' . $A2);
            $objActSheet->mergeCells('S' . $A2 . ':' . 'V' . $A2);

            $objActSheet->mergeCells('A' . $A3 . ':' . 'F' . $A3);
            $objActSheet->mergeCells('G' . $A3 . ':' . 'G' . $A3);
            $objActSheet->mergeCells('H' . $A3 . ':' . 'I' . $A3);
            $objActSheet->mergeCells('J' . $A3 . ':' . 'O' . $A3);
            $objActSheet->mergeCells('P' . $A3 . ':' . 'Q' . $A3);
            $objActSheet->mergeCells('R' . $A3 . ':' . 'R' . $A3);
            $objActSheet->mergeCells('S' . $A3 . ':' . 'V' . $A3);

            $objActSheet->mergeCells('A' . $A4 . ':' . 'E' . $A4);
            $objActSheet->mergeCells('F' . $A4 . ':' . 'G' . $A4);
            $objActSheet->mergeCells('H' . $A4 . ':' . 'O' . $A4);
            $objActSheet->mergeCells('P' . $A4 . ':' . 'Q' . $A4);
            $objActSheet->mergeCells('R' . $A4 . ':' . 'V' . $A4);

            $objActSheet->mergeCells('A' . $A5 . ':' . 'E' . $A5);
            $objActSheet->mergeCells('F' . $A5 . ':' . 'G' . $A5);
            $objActSheet->mergeCells('H' . $A5 . ':' . 'O' . $A5);
            $objActSheet->mergeCells('P' . $A5 . ':' . 'Q' . $A5);
            $objActSheet->mergeCells('R' . $A5 . ':' . 'V' . $A5);

            $objActSheet->mergeCells('A' . $A6 . ':' . 'A' . $A7);
            $objActSheet->mergeCells('B' . $A6 . ':' . 'B' . $A7);
            $objActSheet->mergeCells('C' . $A6 . ':' . 'C' . $A7);
            $objActSheet->mergeCells('D' . $A6 . ':' . 'D' . $A7);
            $objActSheet->mergeCells('E' . $A6 . ':' . 'E' . $A7);
            $objActSheet->mergeCells('F' . $A6 . ':' . 'F' . $A7);
            $objActSheet->mergeCells('G' . $A6 . ':' . 'G' . $A7);
            $objActSheet->mergeCells('H' . $A6 . ':' . 'H' . $A7);
            $objActSheet->mergeCells('I' . $A6 . ':' . 'I' . $A7);
            $objActSheet->mergeCells('J' . $A6 . ':' . 'J' . $A7);
            $objActSheet->mergeCells('K' . $A6 . ':' . 'K' . $A7);
            $objActSheet->mergeCells('K' . $A6 . ':' . 'K' . $A7);
            $objActSheet->mergeCells('L' . $A6 . ':' . 'M' . $A6);
            $objActSheet->mergeCells('N' . $A6 . ':' . 'N' . $A7);
            $objActSheet->mergeCells('O' . $A6 . ':' . 'O' . $A7);
            $objActSheet->mergeCells('P' . $A6 . ':' . 'P' . $A7);
            $objActSheet->mergeCells('Q' . $A6 . ':' . 'Q' . $A7);
            $objActSheet->mergeCells('R' . $A6 . ':' . 'V' . $A6);
            $objActSheet->mergeCells('B' . $start . ':' . 'B' . $end);
            $objActSheet->mergeCells('C' . $start . ':' . 'C' . $end);
            $objActSheet->mergeCells('D' . $start . ':' . 'D' . $end);
            //Excel的第A列，uid是你查出数组的键值，下面以此类推
            $objActSheet->setCellValue('A' . $line, 'FMEA - Failure Mode and Effects Analysis');
            $objActSheet->setCellValue('A' . $A2, 'Issuer (dept, name, phone, sign.)');
            $objActSheet->setCellValue('G' . $A2, 'Date');
            $objActSheet->setCellValue('H' . $A2, 'Function');
            $objActSheet->setCellValue('J' . $A2, 'Process layout-Issue');
            $objActSheet->setCellValue('P' . $A2, 'Reg No');
            $objActSheet->setCellValue('R' . $A2, 'Issue');
            $objActSheet->setCellValue('S' . $A2, 'Page');

            $objActSheet->setCellValue('A' . $A4, 'Change Order');
            $objActSheet->setCellValue('F' . $A4, 'Part No');
            $objActSheet->setCellValue('H' . $A4, 'Part Name');
            $objActSheet->setCellValue('P' . $A4, 'Drawing No-Issue');
            $objActSheet->setCellValue('R' . $A4, 'Supplier');

            $objActSheet->setCellValue('A' . $A3, $v['issuer']);
            $objActSheet->setCellValue('G' . $A3, date('Y.m.d',$v['date']));
            $objActSheet->setCellValue('H' . $A3, $v['function']);
            $objActSheet->setCellValue('J' . $A3, $v['layout']);
            $objActSheet->setCellValue('P' . $A3, $v['reg']);
            $objActSheet->setCellValue('R' . $A3, $v['issue']);
            $objActSheet->setCellValue('S' . $A3, $v['page']);

            $objActSheet->setCellValue('A' . $A5, $v['change']);
            $objActSheet->setCellValue('F' . $A5, $v['part_no']);
            $objActSheet->setCellValue('H' . $A5, $v['part_name']);
            $objActSheet->setCellValue('P' . $A5, $v['drawing']);
            $objActSheet->setCellValue('R' . $A5, $v['supplier']);



            $objActSheet->setCellValue('A' . $A6, 'No');
            $objActSheet->setCellValue('B' . $A6, 'Type');
            $objActSheet->setCellValue('C' . $A6, 'Variant');
            $objActSheet->setCellValue('D' . $A6, 'Plant');
            $objActSheet->setCellValue('E' . $A6, 'Process, part component-number');
            $objActSheet->setCellValue('F' . $A6, 'Potential Failure Mode');
            $objActSheet->setCellValue('G' . $A6, 'Potential Effect(s) of Failure');
            $objActSheet->setCellValue('H' . $A6, 'Sev');
            $objActSheet->setCellValue('I' . $A6, 'Potential Cause');
            $objActSheet->setCellValue('J' . $A6, 'Occur');
            $objActSheet->setCellValue('K' . $A6, 'Class');
            $objActSheet->setCellValue('L' . $A6, 'Current Process Controls');
            $objActSheet->setCellValue('L' . $A7, 'Prevention');
            $objActSheet->setCellValue('M' . $A7, 'Detection');
            $objActSheet->setCellValue('N' . $A6, 'Detec');
            $objActSheet->setCellValue('O' . $A6, 'RPN');
            $objActSheet->setCellValue('P' . $A6, 'Recommended Action(s)');
            $objActSheet->setCellValue('Q' . $A6, 'Responsibility & Target Completion Date');
            $objActSheet->setCellValue('R' . $A6, 'Action Results');
            $objActSheet->setCellValue('R' . $A7, 'Actions Taken & Completion Date');
            $objActSheet->setCellValue('S' . $A7, 'Sev');
            $objActSheet->setCellValue('T' . $A7, 'Occur');
            $objActSheet->setCellValue('U' . $A7, 'Detec');
            $objActSheet->setCellValue('V' . $A7, 'RPN');
            $objActSheet->setCellValue('B' . $A8, $v['type']);
            $objActSheet->setCellValue('C' . $A8, $v['variant']);
            $objActSheet->setCellValue('D' . $A8, $v['plant']);
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV' , $A6);
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV' , $A7);

            if ($v['extends']) {
                foreach ($v['extends'] as $kk => $vv) {
                    $insert = $kk + $start;

                    $objActSheet->setCellValue('A' . $insert, $vv['no']);
                    $objActSheet->setCellValue('E' . $insert, $vv['process']);
                    $objActSheet->setCellValue('F' . $insert, $vv['failure']);
                    $objActSheet->setCellValue('G' . $insert, $vv['effect']);
                    $objActSheet->setCellValue('H' . $insert, $vv['sev']);
                    $objActSheet->setCellValue('I' . $insert, $vv['cause']);
                    $objActSheet->setCellValue('J' . $insert, $vv['occur']);
                    $objActSheet->setCellValue('K' . $insert, $vv['class']);
                    $objActSheet->setCellValue('L' . $insert, $vv['prevention']);
                    $objActSheet->setCellValue('M' . $insert, $vv['detection']);
                    $objActSheet->setCellValue('N' . $insert, $vv['detec']);
                    $objActSheet->setCellValue('O' . $insert, $vv['rpn']);
                    $objActSheet->setCellValue('P' . $insert, $vv['recommended']);
                    $objActSheet->setCellValue('Q' . $insert, $vv['responsibility']?date('Y.m.d',$vv['responsibility']):'');
                    $objActSheet->setCellValue('R' . $insert, $vv['a_actions']?date('Y.m.m',$vv['a_actions']):'');
                    $objActSheet->setCellValue('S' . $insert, $vv['a_sev']);
                    $objActSheet->setCellValue('T' . $insert, $vv['a_occur']);
                    $objActSheet->setCellValue('U' . $insert, $vv['a_detec']);
                    $objActSheet->setCellValue('V' . $insert, $vv['a_rpn']);

                    $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV', $insert);
                }
            }

            //整体样式
            $objStyleB1 = $objActSheet->getStyle('A' . $line . ':' . 'V' . $end);
            //设置字体
            $objFontB1 = $objStyleB1->getFont();
            $objFontB1->setName('宋体');
            $objFontB1->setSize(10);
            //$objFontB1->setBold(true);
            //$objFontA5->getColor()->setARGB('FF999999');
            //设置对齐方式
            $objAlignB1 = $objStyleB1->getAlignment();
            $objAlignB1->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objAlignB1->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //设置单元格宽
            $objActSheet->getColumnDimension('A')->setWidth(4);
            $objActSheet->getColumnDimension('E')->setWidth(12);
            $objActSheet->getColumnDimension('F')->setWidth(12);
            $objActSheet->getColumnDimension('G')->setWidth(12);
            $objActSheet->getColumnDimension('I')->setWidth(12);
            $objActSheet->getColumnDimension('L')->setWidth(16);
            $objActSheet->getColumnDimension('K')->setWidth(16);
            $objActSheet->getColumnDimension('M')->setWidth(12);
            $objActSheet->getColumnDimension('P')->setWidth(16);
            $objActSheet->getColumnDimension('Q')->setWidth(18);
            $objActSheet->getColumnDimension('R')->setWidth(16);
            //设置行高
            $objActSheet->getRowDimension($line)->setRowHeight(30);
            $objActSheet->getRowDimension($A6)->setRowHeight(60);
            $objActSheet->getRowDimension($A7)->setRowHeight(60);
            //项目功能行加粗显示
            $objStyleProject = $objActSheet->getStyle('A' . $A6 . ':' . 'V' . $A7);
            $objFontProject = $objStyleProject->getFont();
            $objFontProject->setBold(true);
            //info字段加粗
            $objStyleProject2 = $objActSheet->getStyle('A' . $A2 . ':' . 'V' . $A2);
            $objFontProject2 = $objStyleProject->getFont();
            $objFontProject2->setBold(true);
            $objStyleProject3 = $objActSheet->getStyle('A' . $A4 . ':' . 'V' . $A4);
            $objFontProject3 = $objStyleProject->getFont();
            $objFontProject3->setBold(true);
            //标题样式
            $objStyleA1 = $objActSheet->getStyle('A' . $line);
            //设置字体
            $objFontA1 = $objStyleA1->getFont();
            $objFontA1->setName('宋体');
            $objFontA1->setSize(14);
            $objFontA1->setBold(true);
            //        $objFontA5->getColor()->setARGB('FF999999');
            //设置对齐方式
            $objAlignA1 = $objStyleA1->getAlignment();
            $objAlignA1->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objAlignA1->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            //设置边框
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV', $line,'Top');
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV', $line,'Bottom');
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV', $line,'Right');
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV', $A2,'Bottom');
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV', $A2,'Right');
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV', $A3,'Bottom');
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV', $A3,'Right');
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV', $A4,'Bottom');
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV', $A4,'Right');
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV', $A5,'Bottom');
            $this->setBorder($objActSheet,'ABCDEFGHIJKLMNOPQRSTUV', $A5,'Right');
        }

        $objActSheet->setTitle("PFMEA EXCEL");
        $filename = iconv("utf-8", "gbk", $v['change']);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename.'.xls');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        $objWriter->save('php://output');
        exit;
    }

}