<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    header("Location: ../auth/login.php");
    exit();
}

// Get client ID from URL
$client_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$client_id) {
    header("Location: model_agency_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Client</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6">Edit Client</h1>
            
            <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"></div>
            <div id="successMessage" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"></div>

            <form id="editClientForm" enctype="multipart/form-data">
                <input type="hidden" id="clientId" name="id">
                <input type="hidden" id="professional" name="professional">
                
                <!-- Basic Information -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
                        <input type="text" id="name" name="name" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="age">Age</label>
                        <input type="number" id="age" name="age" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                    </div>
                </div>

                <!-- Phone and Gender -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="gender">Gender</label>
                        <select id="gender" name="gender" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <!-- City and Language -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="city">City</label>
                        <input type="text" id="city" name="city" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="language">Languages</label>
                        <input type="text" id="language" name="language" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                    </div>
                </div>

                <!-- Artist Fields -->
                <div id="artistFields" class="hidden">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="category">Category</label>
                            <select id="category" name="category" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                <option value="">Select Category</option>
                                <option value="Dating App Host">Dating App Host</option>
                                <option value="Video Live Streamers">Video Live Streamers</option>
                                <option value="Voice Live Streamers">Voice Live Streamers</option>
                                <option value="Singer">Singer</option>
                                <option value="Dancer">Dancer</option>
                                <option value="Actor / Actress">Actor / Actress</option>
                                <option value="Model">Model</option>
                                <option value="Artist / Painter">Artist / Painter</option>
                                <option value="Social Media Influencer">Social Media Influencer</option>
                                <option value="Content Creator">Content Creator</option>
                                <option value="Vlogger">Vlogger</option>
                                <option value="Gamer / Streamer">Gamer / Streamer</option>
                                <option value="YouTuber">YouTuber</option>
                                <option value="Anchor / Emcee / Host">Anchor / Emcee / Host</option>
                                <option value="DJ / Music Producer">DJ / Music Producer</option>
                                <option value="Photographer / Videographer">Photographer / Videographer</option>
                                <option value="Makeup Artist / Hair Stylist">Makeup Artist / Hair Stylist</option>
                                <option value="Fashion Designer / Stylist">Fashion Designer / Stylist</option>
                                <option value="Fitness Trainer / Yoga Instructor">Fitness Trainer / Yoga Instructor</option>
                                <option value="Motivational Speaker / Life Coach">Motivational Speaker / Life Coach</option>
                                <option value="Chef / Culinary Artist">Chef / Culinary Artist</option>
                                <option value="Child Artist">Child Artist</option>
                                <option value="Pet Performer / Pet Model">Pet Performer / Pet Model</option>
                                <option value="Instrumental Musician">Instrumental Musician</option>
                                <option value="Director / Scriptwriter / Editor">Director / Scriptwriter / Editor</option>
                                <option value="Voice Over Artist">Voice Over Artist</option>
                                <option value="Magician / Illusionist">Magician / Illusionist</option>
                                <option value="Stand-up Comedian">Stand-up Comedian</option>
                                <option value="Mimicry Artist">Mimicry Artist</option>
                                <option value="Poet / Storyteller">Poet / Storyteller</option>
                                <option value="Language Trainer / Public Speaking Coach">Language Trainer / Public Speaking Coach</option>
                                <option value="Craft Expert / DIY Creator">Craft Expert / DIY Creator</option>
                                <option value="Travel Blogger / Explorer">Travel Blogger / Explorer</option>
                                <option value="Astrologer / Tarot Reader">Astrologer / Tarot Reader</option>
                                <option value="Educator / Subject Matter Expert">Educator / Subject Matter Expert</option>
                                <option value="Tech Reviewer / Gadget Expert">Tech Reviewer / Gadget Expert</option>
                                <option value="Unboxing / Product Reviewer">Unboxing / Product Reviewer</option>
                                <option value="Business Coach / Startup Mentor">Business Coach / Startup Mentor</option>
                                <option value="Health & Wellness Coach">Health & Wellness Coach</option>
                                <option value="Event Anchor / Wedding Host">Event Anchor / Wedding Host</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="followers">Followers</label>
                            <input type="text" id="followers" name="followers" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <!-- New Artist Fields -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                            <input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="influencer_category">Influencer Category</label>
                            <select id="influencer_category" name="influencer_category" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                <option value="">Select Influencer Category</option>
                                <option value="Comedy and Entertainment Influencers">Comedy and Entertainment Influencers</option>
                                <option value="Fashion and Beauty Influencers">Fashion and Beauty Influencers</option>
                                <option value="Travel and Adventure Influencers">Travel and Adventure Influencers</option>
                                <option value="Food and Cooking Influencers">Food and Cooking Influencers</option>
                                <option value="Fitness and Health Influencers">Fitness and Health Influencers</option>
                                <option value="Lifestyle and Family Influencers">Lifestyle and Family Influencers</option>
                                <option value="Tech and Gaming Influencers">Tech and Gaming Influencers</option>
                                <option value="Business and Educational Influencers">Business and Educational Influencers</option>
                                <option value="Art and Creative Influencers">Art and Creative Influencers</option>
                                <option value="Social Cause and Activism Influencers">Social Cause and Activism Influencers</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="influencer_type">Influencer Type</label>
                            <select id="influencer_type" name="influencer_type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                <option value="">Select Influencer Type</option>
                                <option value="Mega-influencers – with more than a million followers (think celebrities)">Mega-influencers (1M+ followers)</option>
                                <option value="Macro-influencers – between 100,000 and 1 million followers">Macro-influencers (100K-1M followers)</option>
                                <option value="Mid-tier influencers – between 50,000 and 100,000 followers">Mid-tier influencers (50K-100K followers)</option>
                                <option value="Micro-influencers – between 10,000 and 50,000 followers">Micro-influencers (10K-50K followers)</option>
                                <option value="Nano-influencers – between 1,000 and 10,000 followers">Nano-influencers (1K-10K followers)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="instagram_profile">Instagram Profile URL</label>
                            <input type="url" id="instagram_profile" name="instagram_profile" placeholder="https://instagram.com/username" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="expected_payment">Expected Payment (₹)</label>
                            <input type="text" id="expected_payment" name="expected_payment" placeholder="e.g. 10000" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                            <p class="text-sm text-gray-500 mt-1">Enter amount in INR</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="work_type_preference">Work Type Preference</label>
                            <input type="text" id="work_type_preference" name="work_type_preference" placeholder="e.g. Full-time, Part-time, Freelance" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Employee Fields -->
                <div id="employeeFields" class="hidden">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="role">Role</label>
                            <select id="role" name="role" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                <option value="">Select Role</option>
                                <option value="General Manager (GM)">General Manager (GM)</option>
                                <option value="Business Development Manager (BDM)">Business Development Manager (BDM)</option>
                                <option value="Project Manager">Project Manager</option>
                                <option value="Human Resources Manager (HR Manager)">Human Resources Manager (HR Manager)</option>
                                <option value="Talent Acquisition Manager">Talent Acquisition Manager</option>
                                <option value="Recruitment Specialist">Recruitment Specialist</option>
                                <option value="Sales Manager">Sales Manager</option>
                                <option value="Marketing Manager">Marketing Manager</option>
                                <option value="Digital Marketing Manager">Digital Marketing Manager</option>
                                <option value="Social Media Manager">Social Media Manager</option>
                                <option value="Brand Manager">Brand Manager</option>
                                <option value="Public Relations Manager">Public Relations Manager</option>
                                <option value="Content Marketing Manager">Content Marketing Manager</option>
                                <option value="Financial Analyst">Financial Analyst</option>
                                <option value="Investment Banker">Investment Banker</option>
                                <option value="Chartered Accountant (CA)">Chartered Accountant (CA)</option>
                                <option value="Risk Manager">Risk Manager</option>
                                <option value="Wealth Manager">Wealth Manager</option>
                                <option value="Software Engineer">Software Engineer</option>
                                <option value="Data Scientist">Data Scientist</option>
                                <option value="Cloud Architect">Cloud Architect</option>
                                <option value="Cyber Security Analyst">Cyber Security Analyst</option>
                                <option value="AI & Machine Learning Engineer">AI & Machine Learning Engineer</option>
                                <option value="IT Manager">IT Manager</option>
                                <option value="Web Developer">Web Developer</option>
                                <option value="UI/UX Designer">UI/UX Designer</option>
                                <option value="Product Manager">Product Manager</option>
                                <option value="Operations Manager">Operations Manager</option>
                                <option value="Supply Chain Manager">Supply Chain Manager</option>
                                <option value="Logistics Manager">Logistics Manager</option>
                                <option value="Quality Assurance Manager">Quality Assurance Manager</option>
                                <option value="Compliance Manager">Compliance Manager</option>
                                <option value="Legal Advisor">Legal Advisor</option>
                                <option value="Corporate Lawyer">Corporate Lawyer</option>
                                <option value="Judge/Magistrate">Judge/Magistrate</option>
                                <option value="Doctor">Doctor</option>
                                <option value="Pharmacist">Pharmacist</option>
                                <option value="Physiotherapist">Physiotherapist</option>
                                <option value="Dietitian/Nutritionist">Dietitian/Nutritionist</option>
                                <option value="Psychologist">Psychologist</option>
                                <option value="Civil Engineer">Civil Engineer</option>
                                <option value="Mechanical Engineer">Mechanical Engineer</option>
                                <option value="Electrical Engineer">Electrical Engineer</option>
                                <option value="Robotics Engineer">Robotics Engineer</option>
                                <option value="Aerospace Engineer">Aerospace Engineer</option>
                                <option value="Professor/Lecturer">Professor/Lecturer</option>
                                <option value="School Principal">School Principal</option>
                                <option value="Corporate Trainer">Corporate Trainer</option>
                                <option value="Educational Consultant">Educational Consultant</option>
                                <option value="Film Director">Film Director</option>
                                <option value="Actor/Actress">Actor/Actress</option>
                                <option value="Content Creator">Content Creator</option>
                                <option value="Journalist">Journalist</option>
                                <option value="Video Editor">Video Editor</option>
                                <option value="Photographer">Photographer</option>
                                <option value="Event Planner">Event Planner</option>
                                <option value="Interior Designer">Interior Designer</option>
                                <option value="Fashion Designer">Fashion Designer</option>
                                <option value="Graphic Designer">Graphic Designer</option>
                                <option value="Customer Support Executive">Customer Support Executive</option>
                                <option value="Telecaller">Telecaller</option>
                                <option value="Office Administrator">Office Administrator</option>
                                <option value="Executive Assistant">Executive Assistant</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="experience">Experience</label>
                            <input type="text" id="experience" name="experience" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Current Salary (Only for Employees) -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="current_salary">Current Salary (₹)</label>
                        <input type="text" id="current_salary" name="current_salary" placeholder="e.g. 50000" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Enter monthly salary in INR</p>
                    </div>

                    <!-- Resume Upload (Only for Employees) -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="resume">Resume</label>
                        <div class="flex items-center space-x-4">
                            <a id="currentResume" href="#" target="_blank" class="hidden bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-file-pdf mr-2"></i>View Current Resume
                            </a>
                            <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Accepted formats: PDF, DOC, DOCX (Max size: 5MB)</p>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="image">Profile Image</label>
                    <div class="flex items-center space-x-4">
                        <img id="currentImage" src="" alt="Current Image" class="w-16 h-16 rounded-full object-cover">
                        <input type="file" id="image" name="image" accept="image/*" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="model_agency_dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-4 rounded-lg shadow-lg flex items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mr-3"></div>
            <p class="text-gray-700">Saving changes...</p>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-check text-green-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Success!</h3>
                <p class="text-sm text-gray-500 mb-6" id="successModalMessage"></p>
                <button onclick="redirectToDashboard()" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                    Back to Dashboard
                </button>
            </div>
        </div>
    </div>

    <script>
        // Fetch and populate client data
        async function fetchClientData() {
            const urlParams = new URLSearchParams(window.location.search);
            const clientId = urlParams.get('id');
            
            try {
                const response = await fetch(`edit_client.php?id=${clientId}`);
                const data = await response.json();
                
                if (data.status === 'success') {
                    const client = data.client;
                    
                    // Set client ID and professional type
                    document.getElementById('clientId').value = client.id;
                    document.getElementById('professional').value = client.professional;
                    
                    // Populate basic fields
                    document.getElementById('name').value = client.name;
                    document.getElementById('age').value = client.age;
                    document.getElementById('phone').value = client.phone;
                    document.getElementById('gender').value = client.gender;
                    document.getElementById('city').value = client.city;
                    document.getElementById('language').value = client.language;
                    
                    // Show appropriate fields based on professional type
                    if (client.professional === 'Artist') {
                        document.getElementById('artistFields').style.display = 'block';
                        document.getElementById('employeeFields').style.display = 'none';
                        
                        // Populate artist fields
                        document.getElementById('category').value = client.category || '';
                        document.getElementById('followers').value = client.followers || '';
                        
                        // Populate new artist fields
                        document.getElementById('email').value = client.email || '';
                        document.getElementById('influencer_category').value = client.influencer_category || '';
                        document.getElementById('influencer_type').value = client.influencer_type || '';
                        document.getElementById('instagram_profile').value = client.instagram_profile || '';
                        document.getElementById('expected_payment').value = client.expected_payment || '';
                        document.getElementById('work_type_preference').value = client.work_type_preference || '';
                    } else {
                        document.getElementById('artistFields').style.display = 'none';
                        document.getElementById('employeeFields').style.display = 'block';
                        
                        // Populate employee fields
                        document.getElementById('role').value = client.role || '';
                        document.getElementById('experience').value = client.experience || '';
                        document.getElementById('current_salary').value = client.current_salary || '';
                        
                        // Handle resume if available
                        if (client.resume_url) {
                            const resumeLink = document.getElementById('currentResume');
                            resumeLink.classList.remove('hidden');
                            
                            // Get the base URL of the website
                            const baseUrl = window.location.origin;
                            
                            // Construct the resume URL
                            const resumeUrl = client.resume_url.startsWith('http') ? 
                                client.resume_url : 
                                `${baseUrl}/${client.resume_url.replace(/^\.\.\//, '')}`;
                            
                            resumeLink.href = resumeUrl;
                        }
                    }
                    
                    // Show current image
                    // Get the base URL of the website
                    const baseUrl = window.location.origin;
                    const imageUrl = client.image_url.startsWith('http') ? 
                        client.image_url : 
                        `${baseUrl}/${client.image_url.replace(/^\.\.\//, '')}`;
                    document.getElementById('currentImage').src = imageUrl;
                    
                } else {
                    showError('Failed to load client data');
                }
            } catch (error) {
                showError('Error loading client data');
                console.error('Error:', error);
            }
        }

        // Handle form submission
        document.getElementById('editClientForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Show loading overlay
            document.getElementById('loadingOverlay').classList.remove('hidden');
            
            try {
                const formData = new FormData(e.target);
                const response = await fetch('edit_client.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                // Hide loading overlay
                document.getElementById('loadingOverlay').classList.add('hidden');
                
                if (data.status === 'success') {
                    // Show success modal
                    document.getElementById('successModalMessage').textContent = data.message;
                    document.getElementById('successModal').classList.remove('hidden');
                } else {
                    showError(data.message);
                }
            } catch (error) {
                // Hide loading overlay
                document.getElementById('loadingOverlay').classList.add('hidden');
                showError('Error updating client');
                console.error('Error:', error);
            }
        });

        function redirectToDashboard() {
            window.location.href = 'model_agency_dashboard.php';
        }

        // Helper functions for showing messages
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            setTimeout(() => errorDiv.classList.add('hidden'), 5000);
        }

        // Load client data when page loads
        document.addEventListener('DOMContentLoaded', fetchClientData);
    </script>
</body>
</html> 