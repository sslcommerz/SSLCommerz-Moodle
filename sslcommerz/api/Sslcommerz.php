<?php
	namespace Sslcommerz\API;

	class Sslcommerz_API
	{
		public static function requestToSSL($store_id, $password, $env, $post_data)
		{
			if($env == "yes"){
				$direct_api_url = "https://securepay.sslcommerz.com/gwprocess/v4/api.php";
			} else{
				$direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";
			}
			
			$post_data['store_id'] = $store_id;
			$post_data['store_passwd'] = $password;

			$handle = curl_init();
			curl_setopt($handle, CURLOPT_URL, $direct_api_url );
			curl_setopt($handle, CURLOPT_TIMEOUT, 30);
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($handle, CURLOPT_POST, 1 );
			curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


			$content = curl_exec($handle );

			$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

			if($code == 200 && !( curl_errno($handle))) {
				curl_close( $handle);
				$sslcommerzResponse = $content;
			} else {
				curl_close( $handle);
				echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
				exit;
			}

			# PARSE THE JSON RESPONSE
			$sslcz = json_decode($sslcommerzResponse, true );

			if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="" ) {
			        # THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
			        # echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
				echo "<meta http-equiv='refresh' content='0;url=".$sslcz['GatewayPageURL']."'>";
				# header("Location: ". $sslcz['GatewayPageURL']);
				exit;
			} else {
				echo "JSON Data parsing error!";
			}
		}
		
		public static function orderValidation($store_id, $password, $env, $val_id)
		{
			if($env == "yes"){
				$direct_api_url = "https://securepay.sslcommerz.com/validator/api/validationserverAPI.php";
			} else{
				$direct_api_url = "https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php";
			}

			$requested_url 	= $direct_api_url."?val_id=".$val_id."&store_id=".$store_id."&store_passwd=".$password."&v=1&format=json";

			$handle = curl_init();
			curl_setopt($handle, CURLOPT_URL, $requested_url);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false); # IF YOU RUN FROM LOCAL PC
			curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); # IF YOU RUN FROM LOCAL PC

			$result = curl_exec($handle);

			$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

			if($code == 200 && !( curl_errno($handle)))
			{
				# TO CONVERT AS ARRAY
				# $result = json_decode($result, true);
				# $status = $result['status'];

				# TO CONVERT AS OBJECT
				$result = json_decode($result, true);
				return $result;
			} 
			else {
				echo "Failed to connect with SSLCOMMERZ";
			}
		}
	}
?>