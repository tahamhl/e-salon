<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
        /* Özel stillerinizi buraya ekleyebilirsiniz */
        .loading-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    <?php if (isset($extraStyles)) echo $extraStyles; ?>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Bildirim Modal -->
    <div x-data="{ show: false, message: '', type: 'success' }" 
         x-show="show" 
         x-on:notification.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => { show = false }, 5000)"
         class="fixed top-4 right-4 z-50">
        <div x-bind:class="{ 
            'bg-green-100 text-green-800 border-green-400': type === 'success',
            'bg-red-100 text-red-800 border-red-400': type === 'error',
            'bg-blue-100 text-blue-800 border-blue-400': type === 'info',
            'bg-yellow-100 text-yellow-800 border-yellow-400': type === 'warning'
        }" class="rounded-lg p-4 shadow-md border-l-4 flex items-center">
            <div x-bind:class="{
                'text-green-500': type === 'success',
                'text-red-500': type === 'error',
                'text-blue-500': type === 'info',
                'text-yellow-500': type === 'warning'
            }" class="mr-3">
                <i x-show="type === 'success'" class="fas fa-check-circle text-xl"></i>
                <i x-show="type === 'error'" class="fas fa-exclamation-circle text-xl"></i>
                <i x-show="type === 'info'" class="fas fa-info-circle text-xl"></i>
                <i x-show="type === 'warning'" class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div>
                <p x-text="message" class="text-sm"></p>
            </div>
            <button @click="show = false" class="ml-auto text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    <!-- Yükleniyor Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white p-5 rounded-lg shadow-lg flex items-center">
            <div class="loading-spinner mr-3 text-blue-600">
                <i class="fas fa-circle-notch text-2xl"></i>
            </div>
            <p class="text-gray-700">Yükleniyor...</p>
        </div>
    </div>
    
    <?php include_once 'navbar.php'; ?>
    
    <main class="container mx-auto px-4 py-8 flex-grow">
        <?php if (isset($_SESSION['flash_messages']) && count($_SESSION['flash_messages']) > 0): ?>
            <div class="mb-6">
                <?php foreach ($_SESSION['flash_messages'] as $msg): ?>
                    <div class="<?php 
                        $alertClass = 'mb-4 p-4 rounded-lg border-l-4 ';
                        switch($msg['type']) {
                            case 'success': 
                                $alertClass .= 'bg-green-100 text-green-800 border-green-500';
                                $icon = 'fa-check-circle';
                                break;
                            case 'error': 
                                $alertClass .= 'bg-red-100 text-red-800 border-red-500';
                                $icon = 'fa-exclamation-circle';
                                break;
                            case 'info': 
                                $alertClass .= 'bg-blue-100 text-blue-800 border-blue-500';
                                $icon = 'fa-info-circle';
                                break;
                            case 'warning': 
                                $alertClass .= 'bg-yellow-100 text-yellow-800 border-yellow-500';
                                $icon = 'fa-exclamation-triangle';
                                break;
                            default: 
                                $alertClass .= 'bg-gray-100 text-gray-800 border-gray-500';
                                $icon = 'fa-bell';
                        }
                        echo $alertClass;
                    ?> ">
                        <div class="flex items-center">
                            <i class="fas <?php echo $icon; ?> mr-3"></i>
                            <p><?php echo $msg['message']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash_messages']); ?>
            </div>
        <?php endif; ?>
        
        <!-- Sayfa içeriği buraya gelecek -->
    </main>
</body>
</html> 