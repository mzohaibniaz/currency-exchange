<?php
namespace SoftEase\BtcHelper;

class Currency
{
    public function exchange(string $from, string $to, $amount)
    {
        return 2.0;
    }
    public function convert($from, $to, $amount) {
        if ($from == $to) {
            return $amount;
        }
        if ($amount == 0) {
            return 0;
        }

        //return 3211.03;

        $cache_key = 'currency_convert_' . $from . '_' . $to . '_' . $amount;
        /*
        if ($price = get_cache($cache_key)) {
            return $price;
        }
        */

        $url = "https://finance.google.com/finance/converter?a=$amount&from=$from&to=$to";

        $request = curl_init();
        $timeOut = 0;
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt($request, CURLOPT_CONNECTTIMEOUT, $timeOut);
        $response = curl_exec($request);
        curl_close($request);
        $regularExpression = '#\<span class=bld\>(.+?)\<\/span\>#s';
        preg_match($regularExpression, $response, $finalData);

        if (empty($response) OR empty($finalData)) {
            return $this->convertWithBlockChain($from, $to, $amount);
        }

        $pricedata = explode(" ", $finalData[1]);
        $price = number_format($pricedata[0], 2, '.', '');

        //save_cache($cache_key, $price, 10);

        return $price;
    }

    public function convertWithBlockChain($from, $to, $amount) {
        if ($from == $to) {
            return $amount;
        }

        if ($amount == 0) {
            return 0;
        }

        $to = strtoupper($to);
        $from = strtoupper($from);

        $currency = $from;
        $value = $amount;

        if ($from == 'BTC') {
            $currency = $to;
            $value = 1;
        }

        $cache_key = 'convert_with_block_chain_' . $currency . '_' . $value;
        /*
        if ($price = get_cache($cache_key)) {
            return $price;
        }
        */

        $url = "https://blockchain.info/tobtc?currency=$currency&value=$value";
        $request = curl_init();
        $timeOut = 0;

        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt($request, CURLOPT_CONNECTTIMEOUT, $timeOut);

        $rate = curl_exec($request);

        curl_close($request);

        if ($from == 'BTC') {
            $amt = (1 / $rate) * $amount;
            //return round($amt, 2);
            $rate = $amt;
        }

        //save_cache($cache_key, round($rate, 2), 10);

        return round($rate, 2);
    }
}