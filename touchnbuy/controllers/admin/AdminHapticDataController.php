<?php
/**
 * Touch 'n buy
 * Copyright (C) 2015 ALAA
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class AdminHapticDataController extends ModuleAdminController {
    public function initContent() {
        parent::initContent();
        $this->ajax = true;
    }

    public function displayAjax() {
        if ($this->errors)
            die(Tools::jsonEncode(array('hasError' => true, 'errors' => $this->errors)));
        else {
            $result='';
            switch (Tools::getValue('method')) {
                case 'upload' :
                    $result=$this->upload(Tools::getValue('id_image'), 'filename goes here');
                    break;
                case 'remove' :
                    $result=$this->remove(Tools::getValue('id_image'));
                    break;
            }
            die(Tools::jsonEncode($result));
        }
    }

    private function upload($id, $tmp_file) {
        $dst=_THEME_PROD_DIR_.$id.'/'.$id.'-haptic.png';
        $src=$_FILES['haptic_'.$id]['tmp_name'];

        return ImageManager::resize($src, $dst, null, null, 'png', true);
    }

    private function remove($id) {
        $url=_THEME_PROD_DIR_.$id.'/'.$id.'-haptic.png';
        @unlink($url);
        return array('done'=>true,'id'=>$id);
    }
}
