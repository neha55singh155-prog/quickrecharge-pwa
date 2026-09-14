<?php
class DataStore {
    private $dataDir;
    private $plansFile;
    private $ordersFile;
    private $settingsFile;
    private $homeFile;
    private $operatorsFile;
    private $checkoutFile;
    private $transactionsFile;
    private $logsFile;

    public function __construct($dataDir) {
        $this->dataDir = $dataDir;
        $this->plansFile = $dataDir . '/plans.json';
        $this->ordersFile = $dataDir . '/orders.json';
        $this->settingsFile = $dataDir . '/settings.json';
        $this->homeFile = $dataDir . '/home.json';
        $this->operatorsFile = $dataDir . '/operators.json';
        $this->checkoutFile = $dataDir . '/checkout.json';
        $this->transactionsFile = $dataDir . '/transactions.json';
        $this->logsFile = $dataDir . '/payment_logs.json';
        $this->init();
    }

    private function init() {
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }

        if (!file_exists($this->plansFile)) {
            $plans = [
                ['id'=>1,'operator'=>'jio','amount'=>199,'validity'=>'56 Days','data'=>'1.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'popular','badge'=>'NEW','active'=>true],
                ['id'=>2,'operator'=>'jio','amount'=>249,'validity'=>'84 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'popular','badge'=>'NEW','active'=>true],
                ['id'=>3,'operator'=>'jio','amount'=>299,'validity'=>'84 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'popular','badge'=>'HOT','active'=>true],
                ['id'=>4,'operator'=>'jio','amount'=>349,'validity'=>'180 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'NEW','active'=>true],
                ['id'=>5,'operator'=>'jio','amount'=>399,'validity'=>'200 Days','data'=>'2.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'POPULAR','active'=>true],
                ['id'=>6,'operator'=>'jio','amount'=>449,'validity'=>'365 Days','data'=>'2.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'BEST','active'=>true],
                ['id'=>7,'operator'=>'jio','amount'=>499,'validity'=>'365 Days','data'=>'3GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'SPECIAL','active'=>true],
                ['id'=>8,'operator'=>'jio','amount'=>599,'validity'=>'365 Days','data'=>'4GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'PREMIUM','active'=>true],
                ['id'=>9,'operator'=>'jio','amount'=>699,'validity'=>'540 Days','data'=>'2.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'PRO','active'=>true],
                ['id'=>10,'operator'=>'jio','amount'=>799,'validity'=>'730 Days','data'=>'3GB/day','calls'=>'Unlimited','sms'=>'150/day','category'=>'unlimited','badge'=>'VIP','active'=>true],
                ['id'=>11,'operator'=>'jio','amount'=>899,'validity'=>'730 Days','data'=>'4GB/day','calls'=>'Unlimited','sms'=>'150/day','category'=>'unlimited','badge'=>'VIP','active'=>true],
                ['id'=>12,'operator'=>'jio','amount'=>999,'validity'=>'2.5 Years','data'=>'3GB/day','calls'=>'Unlimited','sms'=>'150/day','category'=>'unlimited','badge'=>'ELITE','active'=>true],
                ['id'=>20,'operator'=>'airtel','amount'=>199,'validity'=>'56 Days','data'=>'1.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'popular','badge'=>'NEW','active'=>true],
                ['id'=>21,'operator'=>'airtel','amount'=>249,'validity'=>'84 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'popular','badge'=>'HOT','active'=>true],
                ['id'=>22,'operator'=>'airtel','amount'=>299,'validity'=>'84 Days','data'=>'2.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'popular','badge'=>'POPULAR','active'=>true],
                ['id'=>23,'operator'=>'airtel','amount'=>349,'validity'=>'180 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'NEW','active'=>true],
                ['id'=>24,'operator'=>'airtel','amount'=>399,'validity'=>'200 Days','data'=>'2.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'BEST','active'=>true],
                ['id'=>25,'operator'=>'airtel','amount'=>449,'validity'=>'365 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'','active'=>true],
                ['id'=>26,'operator'=>'airtel','amount'=>499,'validity'=>'365 Days','data'=>'2.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'SPECIAL','active'=>true],
                ['id'=>27,'operator'=>'airtel','amount'=>599,'validity'=>'365 Days','data'=>'3GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'PREMIUM','active'=>true],
                ['id'=>28,'operator'=>'airtel','amount'=>699,'validity'=>'540 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'PRO','active'=>true],
                ['id'=>29,'operator'=>'airtel','amount'=>799,'validity'=>'730 Days','data'=>'2.5GB/day','calls'=>'Unlimited','sms'=>'150/day','category'=>'unlimited','badge'=>'VIP','active'=>true],
                ['id'=>30,'operator'=>'airtel','amount'=>999,'validity'=>'2.5 Years','data'=>'3GB/day','calls'=>'Unlimited','sms'=>'150/day','category'=>'unlimited','badge'=>'ELITE','active'=>true],
                ['id'=>40,'operator'=>'vi','amount'=>179,'validity'=>'56 Days','data'=>'1.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'popular','badge'=>'NEW','active'=>true],
                ['id'=>41,'operator'=>'vi','amount'=>249,'validity'=>'84 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'popular','badge'=>'HOT','active'=>true],
                ['id'=>42,'operator'=>'vi','amount'=>299,'validity'=>'84 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'popular','badge'=>'POPULAR','active'=>true],
                ['id'=>43,'operator'=>'vi','amount'=>349,'validity'=>'180 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'NEW','active'=>true],
                ['id'=>44,'operator'=>'vi','amount'=>399,'validity'=>'200 Days','data'=>'2.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'BEST','active'=>true],
                ['id'=>45,'operator'=>'vi','amount'=>449,'validity'=>'365 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'','active'=>true],
                ['id'=>46,'operator'=>'vi','amount'=>499,'validity'=>'365 Days','data'=>'3GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'SPECIAL','active'=>true],
                ['id'=>47,'operator'=>'vi','amount'=>599,'validity'=>'365 Days','data'=>'3.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'PREMIUM','active'=>true],
                ['id'=>48,'operator'=>'vi','amount'=>699,'validity'=>'540 Days','data'=>'2.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'PRO','active'=>true],
                ['id'=>49,'operator'=>'vi','amount'=>799,'validity'=>'730 Days','data'=>'3GB/day','calls'=>'Unlimited','sms'=>'150/day','category'=>'unlimited','badge'=>'VIP','active'=>true],
                ['id'=>50,'operator'=>'vi','amount'=>999,'validity'=>'2.5 Years','data'=>'3GB/day','calls'=>'Unlimited','sms'=>'150/day','category'=>'unlimited','badge'=>'ELITE','active'=>true],
                ['id'=>60,'operator'=>'bsnl','amount'=>187,'validity'=>'56 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'popular','badge'=>'BEST','active'=>true],
                ['id'=>61,'operator'=>'bsnl','amount'=>247,'validity'=>'84 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'popular','badge'=>'NEW','active'=>true],
                ['id'=>62,'operator'=>'bsnl','amount'=>299,'validity'=>'84 Days','data'=>'2.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'popular','badge'=>'HOT','active'=>true],
                ['id'=>63,'operator'=>'bsnl','amount'=>347,'validity'=>'180 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'','active'=>true],
                ['id'=>64,'operator'=>'bsnl','amount'=>399,'validity'=>'200 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'POPULAR','active'=>true],
                ['id'=>65,'operator'=>'bsnl','amount'=>447,'validity'=>'365 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'BEST','active'=>true],
                ['id'=>66,'operator'=>'bsnl','amount'=>499,'validity'=>'365 Days','data'=>'2.5GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'SPECIAL','active'=>true],
                ['id'=>67,'operator'=>'bsnl','amount'=>599,'validity'=>'365 Days','data'=>'3GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'PREMIUM','active'=>true],
                ['id'=>68,'operator'=>'bsnl','amount'=>699,'validity'=>'540 Days','data'=>'2GB/day','calls'=>'Unlimited','sms'=>'100/day','category'=>'unlimited','badge'=>'PRO','active'=>true],
                ['id'=>69,'operator'=>'bsnl','amount'=>799,'validity'=>'730 Days','data'=>'3GB/day','calls'=>'Unlimited','sms'=>'150/day','category'=>'unlimited','badge'=>'VIP','active'=>true],
                ['id'=>70,'operator'=>'bsnl','amount'=>999,'validity'=>'2.5 Years','data'=>'3GB/day','calls'=>'Unlimited','sms'=>'150/day','category'=>'unlimited','badge'=>'ELITE','active'=>true],
            ];
            file_put_contents($this->plansFile, json_encode($plans, JSON_PRETTY_PRINT));
        }

        if (!file_exists($this->ordersFile)) {
            $orders = [
                ['id'=>1001,'mobile'=>'9876543210','operator'=>'jio','amount'=>299,'plan'=>'2GB/day - 84 Days','status'=>'success','created'=>date('Y-m-d H:i:s', strtotime('-2 days'))],
                ['id'=>1002,'mobile'=>'9123456789','operator'=>'airtel','amount'=>449,'plan'=>'2GB/day - 365 Days','status'=>'success','created'=>date('Y-m-d H:i:s', strtotime('-1 day'))],
                ['id'=>1003,'mobile'=>'8765432109','operator'=>'vi','amount'=>399,'plan'=>'2.5GB/day - 200 Days','status'=>'pending','created'=>date('Y-m-d H:i:s')],
                ['id'=>1004,'mobile'=>'7654321098','operator'=>'jio','amount'=>599,'plan'=>'4GB/day - 365 Days','status'=>'success','created'=>date('Y-m-d H:i:s', strtotime('-3 hours'))],
                ['id'=>1005,'mobile'=>'6543210987','operator'=>'bsnl','amount'=>347,'plan'=>'2GB/day - 180 Days','status'=>'failed','created'=>date('Y-m-d H:i:s', strtotime('-5 hours'))],
            ];
            file_put_contents($this->ordersFile, json_encode($orders, JSON_PRETTY_PRINT));
        }

        if (!file_exists($this->settingsFile)) {
            $settings = [
                'app_name' => 'Recharge App',
                'maintenance_mode' => '0',
                'min_recharge' => '10',
                'max_recharge' => '10000',
                'support_email' => 'support@rechargeapp.com',
                'UPI_ID' => 'recharge@upi',
            ];
            file_put_contents($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
        }

        if (!file_exists($this->homeFile)) {
            $home = [
                'top_banner_slides' => [
                    ['id' => 1, 'title' => 'Cashback Offer', 'subtitle' => 'Get 10% cashback on first recharge', 'bg_color' => '#5F259F', 'accent_color' => '#FFD54F', 'icon' => '💰', 'active' => true, 'order' => 1],
                    ['id' => 2, 'title' => 'Unlimited Plans', 'subtitle' => 'Starting from ₹199 only', 'bg_color' => '#ED1C24', 'accent_color' => '#FFFFFF', 'icon' => '🔥', 'active' => true, 'order' => 2],
                    ['id' => 3, 'title' => 'Family Pack', 'subtitle' => 'Recharge for 4 connections', 'bg_color' => '#0A3D91', 'accent_color' => '#90CAF9', 'icon' => '👨‍👩‍👧‍👦', 'active' => true, 'order' => 3],
                    ['id' => 4, 'title' => '5G Upgrade', 'subtitle' => 'Free 5G SIM delivery', 'bg_color' => '#00897B', 'accent_color' => '#B2DFDB', 'icon' => '⚡', 'active' => true, 'order' => 4],
                ],
                'app_name' => 'QuickRecharge',
                'app_tagline' => 'Fast • Secure • Instant',
                'offer_title' => 'Special Offer Ends In',
                'offer_minutes' => 9,
                'offer_seconds' => 48,
                'promo_brand' => 'PhonePe',
                'promo_title' => 'BEST SAVINGS OFFERS',
                'promo_subtitle' => 'Save more on every recharge • Limited period',
                'promo_hot_deal' => 'HOT DEAL — 2M+ Recharges',
                'promo_plans' => [
                    ['price' => 299, 'data' => '2GB/DAY', 'validity' => '84 DAYS', 'calls' => 'Unlimited', 'featured' => false, 'ribbon' => ''],
                    ['price' => 399, 'data' => '2GB/DAY', 'validity' => '180 DAYS', 'calls' => 'Unlimited', 'featured' => true, 'ribbon' => 'MOST PICKED'],
                    ['price' => 499, 'data' => '3GB/DAY', 'validity' => '365 DAYS', 'calls' => 'Unlimited', 'featured' => false, 'ribbon' => ''],
                    ['price' => 599, 'data' => '4GB/DAY', 'validity' => '365 DAYS', 'calls' => 'Unlimited', 'featured' => false, 'ribbon' => 'BEST VALUE'],
                    ['price' => 699, 'data' => '2.5GB/DAY', 'validity' => '540 DAYS', 'calls' => 'Unlimited', 'featured' => false, 'ribbon' => ''],
                    ['price' => 799, 'data' => '3GB/DAY', 'validity' => '730 DAYS', 'calls' => 'Unlimited', 'featured' => false, 'ribbon' => 'PREMIUM'],
                ],
                'stats' => [
                    ['value' => '4.8★', 'label' => 'App rating'],
                    ['value' => '2M+', 'label' => 'Recharges'],
                    ['value' => '< 5s', 'label' => 'Recharge time'],
                    ['value' => '24/7', 'label' => 'Support'],
                ],
                'trust_items' => [
                    'Protected',
                    'Instant Plans',
                    'Verified',
                ],
            ];
            file_put_contents($this->homeFile, json_encode($home, JSON_PRETTY_PRINT));
        }

        if (!file_exists($this->operatorsFile)) {
            $operators = [
                ['id'=>1,'name'=>'Jio','code'=>'jio','color'=>'linear-gradient(135deg,#0A3D91,#1E6DD1)','image'=>'','active'=>true,'order'=>1],
                ['id'=>2,'name'=>'Airtel','code'=>'airtel','color'=>'linear-gradient(135deg,#ED1C24,#FF3B30)','image'=>'','active'=>true,'order'=>2],
                ['id'=>3,'name'=>'Vi','code'=>'vi','color'=>'linear-gradient(135deg,#CC0000,#FF1A1A)','image'=>'','active'=>true,'order'=>3],
                ['id'=>4,'name'=>'BSNL','code'=>'bsnl','color'=>'linear-gradient(135deg,#0A8A4B,#22C55E)','image'=>'','active'=>true,'order'=>4],
            ];
            file_put_contents($this->operatorsFile, json_encode($operators, JSON_PRETTY_PRINT));
        }

        if (!file_exists($this->checkoutFile)) {
            $checkout = [
                'hero_title' => 'Secure Checkout',
                'hero_sub' => 'UPI • Verified • Fast',
                'upi_id' => 'merchant@upi',
                'merchant_name' => 'QuickRecharge',
                'merchant_display_name' => 'QuickRecharge Store',
                'currency' => 'INR',
                'description' => 'Mobile Recharge',
                'qr_image' => '',
                'upi_apps' => [
                    ['id' => 'phonepe', 'name' => 'PhonePe', 'desc' => 'Recommended • Fastest', 'badge' => 'Popular', 'color' => '#5F259F', 'logo' => '', 'active' => true],
                    ['id' => 'gpay', 'name' => 'Google Pay', 'desc' => 'One tap pay', 'badge' => '', 'color' => '#4285F4', 'logo' => '', 'active' => true],
                    ['id' => 'paytm', 'name' => 'Paytm', 'desc' => 'One tap pay', 'badge' => '', 'color' => '#00BAF2', 'logo' => '', 'active' => true],
                    ['id' => 'qr', 'name' => 'UPI QR Code', 'desc' => 'Scan and pay with any UPI app', 'badge' => '', 'color' => '#333333', 'logo' => '', 'active' => true],
                ],
                'security_text' => '100% Secure Payments',
                'security_sub' => 'Your payment is protected with UPI and bank-level security',
                'trust_items' => ['Secure', 'Instant Recharge', 'Trusted by Millions'],
                'summary_title' => 'Recharge Summary',
                'summary_sub' => 'Please check your details before payment',
                'qr_expiry_minutes' => 5,
            ];
            file_put_contents($this->checkoutFile, json_encode($checkout, JSON_PRETTY_PRINT));
        }

        if (!file_exists($this->transactionsFile)) {
            file_put_contents($this->transactionsFile, json_encode([], JSON_PRETTY_PRINT));
        }

        if (!file_exists($this->logsFile)) {
            file_put_contents($this->logsFile, json_encode([], JSON_PRETTY_PRINT));
        }
        // Backfill qr_expiry for older installs
        $ck = $this->readJSON($this->checkoutFile);
        if (is_array($ck) && !isset($ck['qr_expiry_minutes'])) {
            $ck['qr_expiry_minutes'] = 5;
            $this->writeJSON($this->checkoutFile, $ck);
        }
    }

    private function readJSON($file) {
        if (!file_exists($file)) return [];
        $content = file_get_contents($file);
        return json_decode($content, true) ?: [];
    }

    private function writeJSON($file, $data) {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        // Use LOCK_EX to prevent race conditions during concurrent writes
        file_put_contents($file, $json, LOCK_EX);
    }

    public function getPlans($operator = null) {
        $plans = $this->readJSON($this->plansFile);
        if ($operator && $operator !== 'all') {
            return array_filter($plans, fn($p) => $p['operator'] === $operator);
        }
        return $plans;
    }

    public function getPlan($id) {
        $plans = $this->readJSON($this->plansFile);
        foreach ($plans as $p) {
            if ($p['id'] == $id) return $p;
        }
        return null;
    }

    public function savePlan($plan) {
        $plans = $this->readJSON($this->plansFile);
        if (!empty($plan['id'])) {
            foreach ($plans as &$p) {
                if ($p['id'] == $plan['id']) {
                    $p = array_merge($p, $plan);
                    break;
                }
            }
        } else {
            $maxId = 0;
            foreach ($plans as $p) {
                if ($p['id'] > $maxId) $maxId = $p['id'];
            }
            $plan['id'] = $maxId + 1;
            $plans[] = $plan;
        }
        $this->writeJSON($this->plansFile, $plans);
    }

    public function deletePlan($id) {
        $plans = $this->readJSON($this->plansFile);
        $plans = array_filter($plans, fn($p) => $p['id'] != $id);
        $this->writeJSON($this->plansFile, array_values($plans));
    }

    public function getOrders() {
        return $this->readJSON($this->ordersFile);
    }

    public function updateOrderStatus($id, $status) {
        $orders = $this->readJSON($this->ordersFile);
        foreach ($orders as &$o) {
            if ($o['id'] == $id) {
                $o['status'] = $status;
                break;
            }
        }
        $this->writeJSON($this->ordersFile, $orders);
    }

    public function getSettings() {
        return $this->readJSON($this->settingsFile);
    }

    public function saveSettings($settings) {
        $this->writeJSON($this->settingsFile, $settings);
    }

    public function getOperators() {
        $ops = $this->readJSON($this->operatorsFile);
        usort($ops, fn($a, $b) => ($a['order'] ?? 99) - ($b['order'] ?? 99));
        return $ops;
    }

    public function getOperator($id) {
        $ops = $this->readJSON($this->operatorsFile);
        foreach ($ops as $o) {
            if ($o['id'] == $id) return $o;
        }
        return null;
    }

    public function saveOperator($op) {
        $ops = $this->readJSON($this->operatorsFile);
        if (!empty($op['id'])) {
            foreach ($ops as &$o) {
                if ($o['id'] == $op['id']) {
                    $o = array_merge($o, $op);
                    break;
                }
            }
        } else {
            $maxId = 0;
            foreach ($ops as $o) { if ($o['id'] > $maxId) $maxId = $o['id']; }
            $op['id'] = $maxId + 1;
            $ops[] = $op;
        }
        $this->writeJSON($this->operatorsFile, $ops);
    }

    public function deleteOperator($id) {
        $ops = $this->readJSON($this->operatorsFile);
        $ops = array_filter($ops, fn($o) => $o['id'] != $id);
        $this->writeJSON($this->operatorsFile, array_values($ops));
    }

    public function updateOperatorOrder($orders) {
        $ops = $this->readJSON($this->operatorsFile);
        foreach ($orders as $id => $ord) {
            foreach ($ops as &$o) {
                if ($o['id'] == $id) { $o['order'] = (int)$ord; break; }
            }
        }
        $this->writeJSON($this->operatorsFile, $ops);
    }

    public function getCheckout() {
        $data = $this->readJSON($this->checkoutFile);
        if (empty($data)) { $this->init(); $data = $this->readJSON($this->checkoutFile); }
        return $data;
    }

    public function saveCheckout($data) {
        $this->writeJSON($this->checkoutFile, $data);
    }

    public function getHomeContent() {
        $data = $this->readJSON($this->homeFile);
        if (empty($data)) {
            $this->init();
            $data = $this->readJSON($this->homeFile);
        }
        return $data;
    }

    public function getTopBannerSlides() {
        $home = $this->getHomeContent();
        return $home['top_banner_slides'] ?? [];
    }

    public function saveTopBannerSlides($slides) {
        $home = $this->getHomeContent();
        $home['top_banner_slides'] = $slides;
        $this->writeJSON($this->homeFile, $home);
    }

    public function saveHomeContent($data) {
        $this->writeJSON($this->homeFile, $data);
    }

    public function getStats() {
        $plans = $this->getPlans();
        $orders = $this->getOrders();
        $transactions = $this->getTransactions();
        $totalRevenue = 0;
        $successCount = 0;
        $pendingCount = 0;
        $failedCount = 0;
        foreach ($transactions as $t) {
            if ($t['status'] === 'SUCCESS') { $totalRevenue += $t['amount']; $successCount++; }
            elseif ($t['status'] === 'PENDING') $pendingCount++;
            elseif ($t['status'] === 'FAILED') $failedCount++;
        }
        return [
            'total_plans' => count($plans),
            'total_orders' => count($orders),
            'total_revenue' => $totalRevenue,
            'success_orders' => $successCount,
            'pending_orders' => $pendingCount,
            'failed_orders' => $failedCount,
            'total_transactions' => count($transactions),
        ];
    }

    public function getTransactions($filter = null) {
        $txns = $this->readJSON($this->transactionsFile);
        if ($filter && $filter !== 'all') {
            $txns = array_filter($txns, fn($t) => $t['status'] === strtoupper($filter));
        }
        return array_reverse($txns);
    }

    public function getTransaction($id) {
        $txns = $this->readJSON($this->transactionsFile);
        foreach ($txns as $t) {
            if ($t['transaction_id'] === $id) return $t;
        }
        return null;
    }

    public function createTransaction($data) {
        $txns = $this->readJSON($this->transactionsFile);
        $expiryMinutes = (int)($data['expires_in'] ?? 300);
        // Crypto-secure transaction ID
        $txn = [
            'transaction_id' => 'RCH' . date('Ymd') . strtoupper(bin2hex(random_bytes(8))),
            'reference_id' => $data['reference_id'] ?? '',
            'mobile' => $data['mobile'] ?? '',
            'operator' => $data['operator'] ?? '',
            'plan_id' => (int)($data['plan_id'] ?? 0),
            'plan_amount' => (float)($data['plan_amount'] ?? 0),
            'amount' => (float)($data['plan_amount'] ?? 0),
            'upi_id' => $data['upi_id'] ?? '',
            'merchant_upi_id' => $data['upi_id'] ?? '',
            'payment_method' => $data['payment_method'] ?? '',
            'upi_reference' => '',
            'gateway_reference' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', time() + $expiryMinutes),
            'status' => 'INITIATED',
        ];
        $txns[] = $txn;
        $this->writeJSON($this->transactionsFile, $txns);
        return $txn['transaction_id'];
    }

    public function updateTransactionStatus($txnId, $status, $referenceId = '') {
        $txns = $this->readJSON($this->transactionsFile);
        foreach ($txns as &$t) {
            if ($t['transaction_id'] === $txnId) {
                $t['status'] = $status;
                if ($referenceId) $t['reference_id'] = $referenceId;
                $t['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        $this->writeJSON($this->transactionsFile, $txns);
    }

    public function createOrder($data) {
        $orders = $this->getOrders();
        $maxId = 0;
        foreach ($orders as $o) { if ($o['id'] > $maxId) $maxId = $o['id']; }
        $order = [
            'id' => $maxId + 1,
            'mobile' => $data['mobile'] ?? '',
            'operator' => $data['operator'] ?? '',
            'amount' => (float)($data['amount'] ?? 0),
            'plan' => $data['plan'] ?? '',
            'status' => 'pending',
            'created' => date('Y-m-d H:i:s'),
        ];
        $orders[] = $order;
        $this->writeJSON($this->ordersFile, $orders);
        return $order['id'];
    }

    // ---------- Payment hardening helpers ----------

    public function getQrExpirySeconds() {
        $ck = $this->getCheckout();
        $mins = (int)($ck['qr_expiry_minutes'] ?? 5);
        if ($mins < 1) $mins = 1;
        if ($mins > 30) $mins = 30;
        return $mins * 60;
    }

    public function getActivePaymentMethods() {
        $ck = $this->getCheckout();
        $apps = $ck['upi_apps'] ?? [];
        $out = [];
        foreach ($apps as $a) {
            if (!empty($a['active']) && in_array($a['id'], ['phonepe', 'gpay', 'paytm', 'qr'], true)) {
                $out[] = $a;
            }
        }
        return $out;
    }

    public function isMethodEnabled($method) {
        foreach ($this->getActivePaymentMethods() as $a) {
            if ($a['id'] === $method) return true;
        }
        return false;
    }

    public function addLog($event, $context = []) {
        $logs = $this->readJSON($this->logsFile);
        $logs[] = [
            'at' => date('Y-m-d H:i:s'),
            'event' => $event,
            'context' => $context,
        ];
        // keep last 2000 entries
        if (count($logs) > 2000) $logs = array_slice($logs, -2000);
        $this->writeJSON($this->logsFile, $logs);
    }

    public function getLogs($limit = 100) {
        $logs = $this->readJSON($this->logsFile);
        return array_slice(array_reverse($logs), 0, $limit);
    }

    /** Mark expired INITIATED/PENDING transactions past expires_at. Returns count. */
    public function expireStaleTransactions() {
        $txns = $this->readJSON($this->transactionsFile);
        $now = time();
        $changed = 0;
        foreach ($txns as &$t) {
            if (in_array($t['status'], ['INITIATED', 'PENDING'], true)
                && !empty($t['expires_at']) && strtotime($t['expires_at']) < $now) {
                $t['status'] = 'EXPIRED';
                $t['updated_at'] = date('Y-m-d H:i:s');
                $changed++;
            }
        }
        if ($changed) $this->writeJSON($this->transactionsFile, $txns);
        return $changed;
    }

    public function setTransactionUtr($txnId, $utr) {
        $txns = $this->readJSON($this->transactionsFile);
        foreach ($txns as &$t) {
            if ($t['transaction_id'] === $txnId) {
                $t['upi_reference'] = substr(preg_replace('/[^A-Za-z0-9]/', '', $utr), 0, 32);
                if (in_array($t['status'], ['INITIATED', 'EXPIRED'], true)) {
                    $t['status'] = 'PENDING';
                }
                $t['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        $this->writeJSON($this->transactionsFile, $txns);
    }
}
