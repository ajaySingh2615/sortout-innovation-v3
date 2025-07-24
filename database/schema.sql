CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'user') NOT NULL DEFAULT 'user',
    status ENUM('pending', 'approved') NOT NULL DEFAULT 'pending', -- Default "pending" for new admins
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE blogs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(500),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE devices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category ENUM('Laptop', 'Desktop', 'CCTV', 'Biometric', 'Printer') NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ✅ Ensure a Super Admin Exists (Insert if not already present)
INSERT INTO users (username, email, password, role, status)
SELECT 'SuperAdmin', 'superadmin@example.com', '$2y$10$hashedpassword123', 'super_admin', 'approved'
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE role = 'super_admin'
);

CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    followers VARCHAR(255) NOT NULL,
    category ENUM(
        'Live Streaming Host', 'Voice Streaming Host', 'Video Calling Host',
        'YouTubers', 'Social Media Influencers', 'Bollywood Artist',
        'Brand Ambassador', 'Mobile/PC Gamers', 'Content Creators',
        'Podcast Hosts', 'Vloggers'
    ) NOT NULL,
    city ENUM(
        'Mumbai', 'Delhi', 'Bangalore', 'Hyderabad', 'Ahmedabad', 'Chennai', 'Kolkata', 
        'Surat', 'Pune', 'Jaipur', 'Lucknow', 'Kanpur', 'Nagpur', 'Indore', 'Thane', 
        'Bhopal', 'Visakhapatnam', 'Pimpri and Chinchwad', 'Patna', 'Vadodara', 
        'Ghaziabad', 'Ludhiana', 'Agra', 'Nashik', 'Faridabad', 'Meerut', 'Rajkot', 
        'Kalyan and Dombivali', 'Vasai Virar', 'Varanasi', 'Srinagar', 'Aurangabad', 
        'Dhanbad', 'Amritsar', 'Navi Mumbai', 'Allahabad', 'Haora', 'Ranchi', 'Gwalior', 
        'Jabalpur', 'Coimbatore', 'Vijayawada', 'Jodhpur', 'Madurai', 'Raipur', 'Kota', 
        'Chandigarh', 'Guwahati', 'Solapur', 'Hubli and Dharwad', 'Bareilly', 'Mysore', 
        'Moradabad', 'Gurgaon', 'Aligarh', 'Jalandhar', 'Tiruchirappalli', 'Bhubaneswar', 
        'Salem', 'Mira and Bhayander', 'Thiruvananthapuram', 'Bhiwandi', 'Saharanpur', 
        'Gorakhpur', 'Guntur', 'Amravati', 'Bikaner', 'Noida', 'Jamshedpur', 'Bhilai Nagar', 
        'Warangal', 'Cuttack', 'Firozabad', 'Kochi', 'Bhavnagar', 'Dehradun', 'Durgapur', 
        'Asansol', 'Nanded Waghala', 'Kolapur', 'Ajmer', 'Gulbarga', 'Loni', 'Ujjain', 
        'Siliguri', 'Ulhasnagar', 'Jhansi', 'Sangli Miraj Kupwad', 'Jammu', 'Nellore', 
        'Mangalore', 'Belgaum', 'Jamnagar', 'Tirunelveli', 'Malegaon', 'Gaya', 'Ambattur', 
        'Jalgaon', 'Udaipur', 'Maheshtala', 'Tiruppur', 'Davanagere', 'Kozhikode', 'Kurnool', 
        'Akola', 'Rajpur Sonarpur', 'Bokaro Steel', 'Bellary', 'Patiala', 'South Dum Dum', 
        'Rajarhat Gopalpur', 'Bhagalpur', 'Agartala', 'Muzaffarnagar', 'Bhatpara', 'Latur', 
        'Panihati', 'Dhule', 'Rohtak', 'Korba', 'Bhilwara', 'Brahmapur Town', 'Muzaffarpur', 
        'Ahmadnagar', 'Mathura', 'Kollam', 'Avadi', 'Kadapa', 'Rajahmundry', 'Bilaspur', 
        'Kamarhati', 'Shahjahanpur', 'Bijapur', 'Rampur', 'Shimoga', 'Chandrapur', 'Junagadh', 
        'Thrissur', 'Alwar', 'Barddhaman', 'Kulti', 'Kakinada', 'Nizamabad', 'Parbhani', 
        'Tumkur', 'Hisar', 'Ozhukarai', 'Biharsharif', 'Darbhanga', 'Panipat', 'Aizawl', 
        'Bally', 'Dewas', 'Tirupati', 'Ichalkaranji', 'Karnal', 'Bathinda', 'Jalna', 
        'Kirari Suleman Nagar', 'Purnia', 'Satna', 'Maunath Bhanjan', 'Barasat', 'Sonipat', 
        'Farrukhabad and Fatehgarh', 'Sagar', 'Raurkela', 'Durg', 'Imphal', 'Ratlam', 
        'Hapur', 'Arrah', 'Karimnagar', 'Anantapur', 'NDMC', 'Etawah', 'Ambernath', 
        'Bharatpur', 'Begusarai', 'Tiruvottiyur', 'North Dum Dum', 'Gandhidham', 'Baranagar', 
        'Puducherry', 'Thoothukkudi', 'Sikar', 'Rewa', 'Mirzapur and Vindhyachal', 'Raichur', 
        'Pali', 'Ramagundam', 'Hardwar', 'Vizianagaram', 'Katihar', 'Nagercoil', 'Ganganagar', 
        'Karawal Nagar', 'Mango', 'Thanjavur', 'Bulandshahr', 'Uluberia', 'Murwara', 'Sambhal', 
        'Singrauli', 'Nadiad', 'Secunderabad', 'Naihati', 'Yamunanagar', 'Bidhan Nagar', 
        'Pallavaram', 'Bidar', 'Munger', 'Panchkula', 'Burhanpur', 'Raurkela Industrial Township', 
        'Kharagpur', 'Dindigul', 'Hospet', 'Gandhinagar', 'Nangloi Jat', 'English Bazar', 
        'Ongole', 'Eluru', 'Deoghar', 'Chapra', 'Haldia', 'Khandwa', 'Puri Town', 'Nandyal', 
        'Morena', 'Amroha', 'Anand', 'Bhind', 'Bhalswa Jahangir Pur', 'Madhyamgram', 'Bhiwani', 
        'Navi Mumbai Panvel Raigad', 'Baharampur', 'Ambala', 'Morvi', 'Fatehpur', 'Rae Bareli', 
        'Khora', 'Bhusawal', 'Orai', 'Bahraich', 'Vellore', 'Mahesana', 'Khammam', 'Sambalpur', 
        'Raiganj', 'Sirsa', 'Dinapur Nizamat', 'Serampore', 'Sultan Pur Majra', 'Guna', 'Jaunpur', 
        'Panvel', 'Shivpuri', 'Surendranagar Dudhrej', 'Unnao', 'Hugli and Chinsurah', 'Sitapur', 
        'Hastsal', 'Tambaram', 'Adityapur', 'Badalapur', 'Alappuzha', 'Cuddalore', 'Silchar', 
        'Gadag and Betigeri', 'Bahadurgarh', 'Machilipatnam', 'Shimla', 'Medinipur', 'Deoli', 
        'Bharuch', 'Hoshiarpur', 'Jind', 'Chandannagar', 'Adoni', 'Tonk', 'Faizabad', 'Tenali', 
        'Alandur', 'Kancheepuram', 'Vapi', 'Rajnandgaon', 'Proddatur', 'Navsari', 'Budaun', 
        'Uttarpara Kotrung', 'Mahbubnagar', 'Erode', 'Batala', 'Saharsa', 'Haldwani and Kathgodam', 
        'Vidisha', 'Thanesar', 'Kishangarh', 'Dallo Pura', 'Veraval', 'Banda', 'Chittoor', 
        'Krishnanagar', 'Barrackpur', 'Lakhimpur', 'Santipur', 'Porbandar', 'Hindupur', 'Balurghat', 
        'Bhadravati', 'Hanumangarh', 'Moga', 'Pathankot', 'Hajipur', 'Sasaram', 'Habra', 'Bid', 
        'Mohali', 'Burari', 'Beawar', 'Abohar', 'Tiruvannamalai', 'Jamuria', 'Kaithal', 'Godhra', 
        'Bhuj', 'Robertson Pet', 'Shillong', 'Rewari', 'Hazaribag', 'Bhimavaram', 'Mandsaur', 
        'Chas', 'Rudrapur', 'Chitradurga', 'Kumbakonam', 'Dibrugarh', 'Kolar', 'Chhindwara', 
        'Bankura', 'Mandya', 'Dehri', 'Raigarh', 'Madanapalle', 'Nalgonda', 'Hathras', 
        'Malerkotla', 'Siwan', 'Chhattarpur', 'Hassan', 'Lalitpur', 'Gondiya', 'North Barrackpur', 
        'Bettiah', 'Palakkad', 'Rajapalayam', 'Botad', 'Modinagar', 'Deoria', 'Raniganj', 
        'Palwal', 'Khanna', 'Neemuch', 'Pilibhit', 'Mustafabad', 'Hardoi', 'Guntakal', 
        'Pithampur', 'Motihari', 'Dhaulpur', 'Srikakulam', 'Nabadwip', 'Patan', 'Jagdalpur', 
        'Udupi', 'Basirhat', 'Damoh', 'Halisahar', 'Jagadhri', 'Rishra', 'Kurichi', 'Dimapur', 
        'Palanpur', 'Dharmavaram', 'Gokal Pur', 'Kashipur', 'Ashokenagar Kalyangarh', 'Baidyabati', 
        'Sawai Madhopur', 'Puruliya', 'Mandoli', 'Mainpuri', 'Kanchrapara', 'Satara', 'Churu', 
        'Madavaram', 'Gangapur', 'Dabgram', 'Darjiling', 'Barshi', 'Etah', 'Jhunjhunun', 
        'Chikmagalur', 'Jetpur Navagadh', 'Roorkee', 'Gudivada', 'Baleshwar', 'Baran', 
        'Hoshangabad', 'Nagaon', 'Pudukkottai', 'Adilabad', 'Hosur', 'Muktsar', 'Yavatmal', 
        'Titagarh', 'Barnala', 'Chittaurgarh', 'Narasaraopet', 'Dum Dum', 'Basti', 'Valsad', 
        'Ambur', 'Giridih', 'Chandausi', 'Gonda', 'Bally Town', 'Kalol', 'Bagaha', 'Ambikapur', 
        'Achalpur', 'Gondal', 'Bagalkot', 'Osmanabad', 'Akbarpur', 'Champdani', 'Deesa', 'Khurja', 
        'Nandurbar', 'Azamgarh', 'Ghazipur', 'Delhi Cantonment', 'Firozpur', 'Baripada', 
        'Mughalsarai', 'Anantnag', 'Sehore', 'Bongaon', 'Kanpur Cantonment', 'Khardaha', 
        'Tadpatri', 'Port Blair', 'Sultanpur', 'Bhadrak', 'Shikohabad', 'Jalpaiguri', 'Shamli', 
        'Karaikkudi', 'Khargone', 'Wardha', 'Ranibennur', 'Kishanganj', 'Neyveli', 'Amreli', 
        'Suryapet', 'Gangawati', 'Hindaun', 'Jamalpur', 'Bhiwadi', 'Ballia', 'Bansberia', 
        'Tadepalligudem', 'Miryalaguda', 'Baraut', 'Udgir', 'Betul', 'Bundi', 'Jehanabad', 
        'Ambala Sadar', 'Nagapattinam', 'Nagaur', 'Buxar', 'Seoni', 'Aurangabad', 'Greater Noida', 
        'Hinganghat', 'Sujangarh', 'Bhadreswar', 'Chilakaluripet', 'Kasganj', 'Kalyani', 'Gangtok', 
        'Datia', 'Nagda', 'Banswara', 'Kapurthala'
    ) NOT NULL,
    language VARCHAR(255) NOT NULL,
    professional ENUM('Artist', 'Employee') NOT NULL,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

