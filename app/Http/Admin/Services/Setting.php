<?php

namespace App\Http\Admin\Services;

use App\Caches\Setting as SettingCache;
use App\Repos\Setting as SettingRepo;
use App\Repos\Vip as VipRepo;

class Setting extends Service
{

    public function getQQAuthSettings()
    {
        $oauth = $this->getSettings('oauth.qq');

        $oauth['redirect_uri'] = $oauth['redirect_uri'] ?: kg_full_url(['for' => 'home.oauth.qq_callback']);

        return $oauth;
    }

    public function getWeixinAuthSettings()
    {
        $oauth = $this->getSettings('oauth.weixin');

        $oauth['redirect_uri'] = $oauth['redirect_uri'] ?: kg_full_url(['for' => 'home.oauth.weixin_callback']);

        return $oauth;
    }

    public function getWeiboAuthSettings()
    {
        $oauth = $this->getSettings('oauth.weibo');

        $oauth['redirect_uri'] = $oauth['redirect_uri'] ?: kg_full_url(['for' => 'home.oauth.weibo_callback']);
        $oauth['refuse_uri'] = $oauth['refuse_uri'] ?: kg_full_url(['for' => 'home.oauth.weibo_refuse']);

        return $oauth;
    }

    public function getAlipaySettings()
    {
        $alipay = $this->getSettings('pay.alipay');

        $alipay['return_url'] = $alipay['return_url'] ?: kg_full_url(['for' => 'home.alipay_callback']);
        $alipay['notify_url'] = $alipay['notify_url'] ?: kg_full_url(['for' => 'home.alipay_notify']);

        return $alipay;
    }

    public function getWxpaySettings()
    {
        $wxpay = $this->getSettings('pay.wxpay');

        $wxpay['return_url'] = $wxpay['return_url'] ?: kg_full_url(['for' => 'home.wxpay_callback']);
        $wxpay['notify_url'] = $wxpay['notify_url'] ?: kg_full_url(['for' => 'home.wxpay_notify']);

        return $wxpay;
    }

    public function getWechatOASettings()
    {
        $oa = $this->getSettings('wechat.oa');

        $oa['notify_url'] = $oa['notify_url'] ?: kg_full_url(['for' => 'home.wechat.oa.notify']);

        return $oa;
    }

    public function getVipSettings()
    {
        $vipRepo = new VipRepo();

        return $vipRepo->findAll(['deleted' => 0]);
    }

    public function getLiveSettings($section)
    {
        $result = $this->getSettings($section);

        if ($section == 'live.notify') {
            $result['stream_begin_url'] = $result['stream_begin_url'] ?: kg_full_url(['for' => 'home.live_notify'], ['action' => 'streamBegin']);
            $result['stream_end_url'] = $result['stream_end_url'] ?: kg_full_url(['for' => 'home.live_notify'], ['action' => 'streamEnd']);
            $result['record_url'] = $result['record_url'] ?: kg_full_url(['for' => 'home.live_notify'], ['action' => 'record']);
            $result['snapshot_url'] = $result['snapshot_url'] ?: kg_full_url(['for' => 'home.live_notify'], ['action' => 'snapshot']);
            $result['porn_url'] = $result['porn_url'] ?: kg_full_url(['for' => 'home.live_notify'], ['action' => 'porn']);
        }

        return $result;
    }

    public function getSettings($section)
    {
        $settingsRepo = new SettingRepo();

        $items = $settingsRepo->findBySection($section);

        $result = [];

        if ($items->count() > 0) {
            foreach ($items as $item) {
                $result[$item->item_key] = $item->item_value;
            }
        }

        return $result;
    }

    public function updateSettings($section, $settings)
    {
        $settingsRepo = new SettingRepo();

        foreach ($settings as $key => $value) {
            $item = $settingsRepo->findItem($section, $key);
            if ($item) {
                $item->item_value = trim($value);
                $item->update();
            }
        }

        $cache = new SettingCache();

        $cache->rebuild($section);
    }

    public function updateStorageSettings($section, $settings)
    {
        $protocol = ['http://', 'https://'];

        if (isset($settings['domain'])) {
            $settings['domain'] = str_replace($protocol, '', $settings['domain']);
        }

        $this->updateSettings($section, $settings);
    }

    public function updateVodSettings($section, $settings)
    {
        $this->updateSettings($section, $settings);
    }

    public function updateLiveSettings($section, $settings)
    {
        $protocol = ['http://', 'https://'];

        if (in_array($section, ['live.push', 'live.pull'])) {
            if (isset($settings['domain'])) {
                $settings['domain'] = str_replace($protocol, '', $settings['domain']);
            }
        }

        $this->updateSettings($section, $settings);
    }

    public function updateSmsSettings($section, $settings)
    {
        if (isset($settings['template'])) {
            $settings['template'] = kg_json_encode($settings['template']);
        }

        $this->updateSettings($section, $settings);
    }

    public function updateVipSettings($items)
    {
        $vipRepo = new VipRepo();

        foreach ($items as $id => $price) {
            $vip = $vipRepo->findById($id);
            $vip->price = $price;
            $vip->update();
        }
    }

    public function updateWechatSettings($section, $settings)
    {
        if ($section == 'wechat.oa') {
            if (isset($settings['notice_template'])) {
                $settings['notice_template'] = kg_json_encode($settings['notice_template']);
            }
        }

        $this->updateSettings($section, $settings);
    }

}
