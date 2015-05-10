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
class TouchNBuy extends Module {
    private $imgMap=array(5=>1,1=>1); //id_image (presta) => id_texture (touchnbuy);


    public function __construct() {
        $this->name = 'touchnbuy';
        $this->tab = 'other';
        $this->version = '0.1';
        $this->author = 'ALAA';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '2.0');
        parent::__construct();
        $this->displayName = $this->l('Touch n buy');
        $this->description = $this->l('Haptic extension for e-Commerce solutions');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install() {
        $tab = new Tab();
        $tab->active = 1;
        $tab->name = array();
        $tab->class_name = 'AdminHapticData';

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'noname';
        }
        $tab->id_parent = -1;
        $tab->module = 'touchnbuy';
        $tab->hide_host_mode=1;
        $tab->add();

        if (!parent::install() OR
            !$this->registerHook('displayAdminProductsExtra') OR
            !$this->registerHook('displayFooterProduct')
        ) {
            return FALSE;
        }

        return TRUE;
    }



    public function hookDisplayAdminProductsExtra($params) {
        $id_product = Tools::getValue('id_product');
        if ($id_product > 0) {
            $_images = Image::getImages($this->context->language->id, $id_product);
            $images=array();
			foreach ($_images as $k => $image) {
				$images[$k] = new Image($image['id_image']);
                $image_uploader = new HelperImageUploader('haptic_'.$image['id_image']);

                $url=Context::getContext()->link->getAdminLink('AdminHapticData').'&ajax=1&&id_image='.(int)$image['id_image'].'&method=upload';

                $image_uploader->setMultiple(false)->setUseAjax(true)->setUrl($url);
                $images[$k]->hapticUploader=$image_uploader->render();
                $images[$k]->hasHaptic=/*file_exists(_PS_PROD_IMG_DIR_.'/'.$image['id_image'].'/'.$image['id_image'].'-haptic.png');*/isset($this->imgMap[$image['id_image']]);
			}
            $this->context->smarty->assign(array(
                'images' => $images,
                'token' => Tools::getAdminTokenLite('AdminHapticData')
            ));
        }
        return $this->display(__FILE__, 'views/admin/touchnbuy.tpl');
    }

    public function hookDisplayFooterProduct($params) {
        $imageIds=array();
        $hapticUrls=array();

        $images = Image::getImages($this->context->language->id, $params['product']->id);
        foreach ($images as $k => $image) {
            if (/*file_exists(_PS_PROD_IMG_DIR_.'/'.$image['id_image'].'/'.$image['id_image'].'-haptic.png')*/isset($this->imgMap[$image['id_image']])) {
                $imageIds[]=$image['id_image'];
                $hapticUrls[]='"openhapticimage/'.$this->imgMap[$image['id_image']].'"';
                //$hapticUrls[]=_THEME_PROD_DIR_.$image['id_image'].'/'.$image['id_image'].'-haptic.png';
            }
        }

        return '<script type="text/javascript">
            var knownImages = ['.join(',',$imageIds).'];
            var hapticUrls = ['.join(',', $hapticUrls).'];
            $(document).ready(function(){
                var windowHasFocus;
                $(window).focus(function() {
                  windowHasFocus = true;
                }).blur(function() {
                  windowHasFocus = false;
                });

                $("[itemprop=image]").each(function(){
                    var iid = $(this).attr("id").split("_");
                    iid = parseInt(iid[1]);
                    var a = $(this.parentNode);
                    var hapticUrlId = knownImages.indexOf(iid);
                    if (hapticUrlId >= 0) {
                        a.removeClass("fancybox");
                        a.attr("data-haptic","touchnbuy://"+hapticUrls[hapticUrlId]);
                        a.click(function (e) {
                            window.location=$(this).attr("data-haptic");
                            setTimeout(function(){
                                if(windowHasFocus) {
                                    $.fancybox({
                                        href: a.attr("href"),
                                        title : a.attr("title")
                                    });
                                }
                            }, 100);
                            e.preventDefault();
                            return false;
                        });
                    }
                });
            });
        </script>';
    }
}
