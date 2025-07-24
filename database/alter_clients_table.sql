-- Add role column to clients table
ALTER TABLE clients ADD COLUMN role VARCHAR(50) DEFAULT NULL;

-- Update existing records to set role as NULL for Artist type
UPDATE clients SET role = NULL WHERE professional = 'Artist';

-- Update existing records to set category as NULL for Employee type
UPDATE clients SET category = NULL WHERE professional = 'Employee';

-- Modify category ENUM to include new categories
ALTER TABLE clients MODIFY COLUMN category ENUM(
    'Live Streaming Host', 'Voice Streaming Host', 'Video Calling Host',
    'YouTubers', 'Social Media Influencers', 'Bollywood Artist',
    'Brand Ambassador', 'Mobile/PC Gamers', 'Content Creators',
    'Podcast Hosts', 'Vloggers'
) NULL;

-- Add city field to clients table
ALTER TABLE clients ADD COLUMN city ENUM(
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
    'Jalgaon', 'Udaipur', 'Maheshtala', 'Tiruppur', 'Davanagere', 'Kozhikode', 'Kurnool'
) NOT NULL DEFAULT 'Mumbai'; 