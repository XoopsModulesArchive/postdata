<?php

// $Id: index.php,v 1.1 2006/03/27 12:19:41 mikhail Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <https://www.xoops.org>                             //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
require dirname(__DIR__, 3) . '/include/cp_header.php';
$myts = MyTextSanitizer::getInstance();

if (isset($_GET['op'])) {
    $op = (int)$_GET['op'];
} else {
    $op = '';
}

function adddata($filen)
{
    global $xoopsDB;

    $addfile = XOOPS_ROOT_PATH . '/modules/postdata/admin/' . $filen;

    $fh = fopen($addfile, 'rb');

    $i = 0;

    if ($fh) {
        while (!feof($fh)) {
            $buf = fgets($fh);

            $sql = str_replace('xoops_postalcode', $xoopsDB->prefix('postalcode'), $buf);

            $sql = str_replace(';', '', $sql);

            if (!empty($sql)) {
                $result = $xoopsDB->queryF($sql);
            }
        }

        fclose($fh);

        if (is_writable($addfile)) {
            @unlink($addfile);
        }
    }
}

switch ($op) {
    case '1':
        $sql = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('postalcode');
        $result = $xoopsDB->query($sql);
        [$numrows] = $xoopsDB->fetchRow($result);
        if ($numrows > 0) {
            redirect_header('index.php', 3, '既に登録されてます。');
        }

        $far = [];
        if ($handle = opendir(XOOPS_ROOT_PATH . '/modules/postdata/admin/ins/')) {
            while (false !== ($fdir = readdir($handle))) {
                if ('.' != $fdir && '..' != $fdir) {
                    $far[] = $fdir;
                }
            }

            closedir($handle);
        }
        if (count($far) < 1) {
            redirect_header('index.php', 3, 'データファイルが見つかりません。');
        }
        redirect_header('index.php?op=3', 1, 'データ登録を開始します。画面の切替りに時間が掛かります。');
        break;
    case '2':
        $sql = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('postalcode');
        $result = $xoopsDB->query($sql);
        [$numrows] = $xoopsDB->fetchRow($result);
        if (0 == $numrows) {
            redirect_header('index.php', 3, '登録されていません。');
        }

        $far = [];
        if ($handle = opendir(XOOPS_ROOT_PATH . '/modules/postdata/admin/up/')) {
            while (false !== ($fdir = readdir($handle))) {
                if ('.' != $fdir && '..' != $fdir) {
                    $far[] = $fdir;
                }
            }

            closedir($handle);
        }
        if (count($far) < 1) {
            redirect_header('index.php', 3, 'データファイルが見つかりません。');
        }
        xoops_cp_header();
        foreach ($far as $val) {
            echo '<br><a href="index.php?op=4&amp;fn=' . $val . '">' . $val . '</a><br>';
        }
        xoops_cp_footer();
        break;
    case '3':
        //新規登録
        $fn = ['data1.dat', 'data2.dat', 'data3.dat', 'data4.dat', 'data5.dat'];
        if (isset($_GET['num'])) {
            $num = (int)$_GET['num'];
        } else {
            $num = 0;
        }
        adddata('ins/' . $fn[$num]);
        if (4 != $num) {
            $jump = 'index.php?op=3&num=' . ($num + 1);

            redirect_header($jump, 1, 'データ' . ($num + 1) . '登録中です。');
        } else {
            redirect_header('index.php', 2, 'データ登録完了。');
        }
        break;
    case '4':
        //更新
        if (empty($_GET['fn'])) {
            redirect_header('index.php', 2, 'エラーです。');
        }
        adddata('up/' . $myts->stripSlashesGPC($_GET['fn']));
        redirect_header('index.php', 2, $myts->stripSlashesGPC($_GET['fn']) . 'データ更新完了。');
        break;
    default:
        xoops_cp_header();
        echo '<a href="index.php?op=1">データ登録</a><br><br>';
        echo '<a href="index.php?op=2">データ更新</a>';
        xoops_cp_footer();
}
