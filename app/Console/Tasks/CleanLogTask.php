<?php

namespace App\Console\Tasks;

class CleanLogTask extends Task
{

    public function mainAction()
    {
        $this->cleanCommonLog();
        $this->cleanConsoleLog();
        $this->cleanHttpLog();
        $this->cleanSqlLog();
        $this->cleanListenLog();
        $this->cleanCaptchaLog();
        $this->cleanWechatLog();
        $this->cleanMailLog();
        $this->cleanSmsLog();
        $this->cleanVodLog();
        $this->cleanLiveLog();
        $this->cleanStorageLog();
        $this->cleanAlipayLog();
        $this->cleanWxpayLog();
        $this->cleanOrderLog();
        $this->cleanRefundLog();
        $this->cleanNoticeLog();
    }

    /**
     * 清理通用日志
     */
    protected function cleanCommonLog()
    {
        $this->cleanLog('common', 7);
    }

    /**
     * 清理Http日志
     */
    protected function cleanHttpLog()
    {
        $this->cleanLog('http', 7);
    }

    /**
     * 清理Console日志
     */
    protected function cleanConsoleLog()
    {
        $this->cleanLog('console', 7);
    }

    /**
     * 清理SQL日志
     */
    protected function cleanSqlLog()
    {
        $this->cleanLog('sql', 3);
    }

    /**
     * 清理监听日志
     */
    protected function cleanListenLog()
    {
        $this->cleanLog('listen', 7);
    }

    /**
     * 清理验证码服务日志
     */
    protected function cleanCaptchaLog()
    {
        $this->cleanLog('captcha', 7);
    }

    /**
     * 清理点播服务日志
     */
    protected function cleanVodLog()
    {
        $this->cleanLog('vod', 7);
    }

    /**
     * 清理直播服务日志
     */
    protected function cleanLiveLog()
    {
        $this->cleanLog('live', 7);
    }

    /**
     * 清理存储服务日志
     */
    protected function cleanStorageLog()
    {
        $this->cleanLog('storage', 7);
    }

    /**
     * 清理短信服务日志
     */
    protected function cleanSmsLog()
    {
        $this->cleanLog('sms', 7);
    }

    /**
     * 清理邮件服务日志
     */
    protected function cleanMailLog()
    {
        $this->cleanLog('mail', 7);
    }

    /**
     * 清理微信服务日志
     */
    protected function cleanWechatLog()
    {
        $this->cleanLog('wechat', 7);
    }

    /**
     * 清理阿里支付服务日志
     */
    protected function cleanAlipayLog()
    {
        $this->cleanLog('alipay', 30);
    }

    /**
     * 清理微信支付服务日志
     */
    protected function cleanWxpayLog()
    {
        $this->cleanLog('wxpay', 30);
    }

    /**
     * 清理订单日志
     */
    protected function cleanOrderLog()
    {
        $this->cleanLog('order', 30);
    }

    /**
     * 清理退款日志
     */
    protected function cleanRefundLog()
    {
        $this->cleanLog('refund', 30);
    }

    /**
     * 清理通知日志
     */
    protected function cleanNoticeLog()
    {
        $this->cleanLog('notice', 7);
    }

    /**
     * 清理日志文件
     *
     * @param string $prefix
     * @param int $keepDays 保留天数
     * @return mixed
     */
    protected function cleanLog($prefix, $keepDays)
    {
        $files = glob(log_path() . "/{$prefix}-*.log");

        if (!$files) return false;

        foreach ($files as $file) {
            $date = substr($file, -14, 10);
            $today = date('Y-m-d');
            if (strtotime($today) - strtotime($date) >= $keepDays * 86400) {
                $deleted = unlink($file);
                if ($deleted) {
                    echo "delete {$file} success" . PHP_EOL;
                } else {
                    echo "delete {$file} failed" . PHP_EOL;
                }
            }
        }
    }

}
