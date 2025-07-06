</main>
    
    <footer class="bg-gray-800 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About Section -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">SIMPRAK</h3>
                    <p class="text-gray-300 text-sm">
                        Sistem Informasi Manajemen Praktikum untuk memudahkan pengelolaan 
                        kegiatan praktikum di institusi pendidikan.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="index.php" class="text-gray-300 hover:text-white">Beranda</a></li>
                        <li><a href="katalog.php" class="text-gray-300 hover:text-white">Katalog Praktikum</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="dashboard.php" class="text-gray-300 hover:text-white">Dashboard</a></li>
                        <?php else: ?>
                            <li><a href="login.php" class="text-gray-300 hover:text-white">Login</a></li>
                            <li><a href="register.php" class="text-gray-300 hover:text-white">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Kontak</h3>
                    <div class="space-y-2 text-sm text-gray-300">
                        <p><i class="fas fa-envelope mr-2"></i>admin@simprak.ac.id</p>
                        <p><i class="fas fa-phone mr-2"></i>+62 21 1234 5678</p>
                        <p><i class="fas fa-map-marker-alt mr-2"></i>Yogyakarta, Indonesia</p>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-300 text-sm">
                        &copy; <?= date('Y') ?> SIMPRAK. All rights reserved.
                    </p>
                    <div class="flex space-x-4 mt-4 md:mt-0">
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>