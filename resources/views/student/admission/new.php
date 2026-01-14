<div x-data="admissionForm()" class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">New Admission Application</h1>
        <p class="text-slate-500 mt-1">Fill out the form below to apply for admission</p>
    </div>
    
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <template x-for="(step, index) in ['Course Selection', 'Personal Info', 'Guardian Info', 'Academic Info', 'Documents']" :key="index">
                <div class="flex items-center">
                    <div :class="currentStep > index ? 'bg-primary-600 text-white' : (currentStep === index ? 'bg-primary-600 text-white' : 'bg-slate-200 text-slate-600')" 
                         class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium">
                        <span x-show="currentStep <= index" x-text="index + 1"></span>
                        <i x-show="currentStep > index" class="fas fa-check text-xs"></i>
                    </div>
                    <span :class="currentStep >= index ? 'text-primary-600' : 'text-slate-400'" 
                          class="hidden sm:block ml-2 text-sm font-medium" x-text="step"></span>
                    <div x-show="index < 4" class="hidden sm:block w-12 lg:w-24 h-0.5 mx-2" :class="currentStep > index ? 'bg-primary-600' : 'bg-slate-200'"></div>
                </div>
            </template>
        </div>
    </div>
    
    <form method="POST" action="<?= BASE_URL ?>/student/admission" enctype="multipart/form-data" @submit="submitting = true">
        <input type="hidden" name="_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>">
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Step 1: Course Selection -->
            <div x-show="currentStep === 0" class="p-6 space-y-6">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-4">Select Course & Campus</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Campus <span class="text-red-500">*</span></label>
                        <select name="campus_id" x-model="formData.campus_id" @change="loadCourses()" required
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select Campus</option>
                            <?php foreach ($campuses as $campus): ?>
                            <option value="<?= $campus['id'] ?>"><?= htmlspecialchars($campus['name']) ?> (<?= ucfirst($campus['type']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Course <span class="text-red-500">*</span></label>
                        <select name="course_id" x-model="formData.course_id" @change="loadFeeStructure()" required
                                :disabled="loading || !formData.campus_id"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:bg-slate-100">
                            <option value="" x-text="getCoursePlaceholder()"></option>
                            <template x-for="course in courses" :key="course.id">
                                <option :value="course.id" x-text="course.name + ' (' + course.code + ')'"></option>
                            </template>
                        </select>
                        <p x-show="loading" class="text-blue-600 text-sm mt-1">
                            <i class="fas fa-spinner fa-spin mr-1"></i>Loading courses...
                        </p>
                        <p x-show="!loading && formData.campus_id && courses.length === 0" class="text-amber-600 text-sm mt-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i>No courses available for this campus.
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Shift <span class="text-red-500">*</span></label>
                        <select name="shift" x-model="formData.shift" required
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="morning">Morning</option>
                            <option value="evening">Evening</option>
                        </select>
                    </div>
                </div>
                
                <!-- Fee Structure Display -->
                <div x-show="feeStructure" class="mt-6 p-4 bg-primary-50 rounded-xl border border-primary-100">
                    <h3 class="font-medium text-primary-800 mb-3"><i class="fas fa-info-circle mr-2"></i>Fee Structure</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-primary-600">Admission Fee</p>
                            <p class="font-semibold text-primary-800">PKR <span x-text="feeStructure?.admission_fee?.toLocaleString() || 0"></span></p>
                        </div>
                        <div>
                            <p class="text-primary-600">Tuition Fee</p>
                            <p class="font-semibold text-primary-800">PKR <span x-text="feeStructure?.tuition_fee?.toLocaleString() || 0"></span></p>
                        </div>
                        <div>
                            <p class="text-primary-600">Total Payable</p>
                            <p class="font-bold text-primary-800">PKR <span x-text="feeStructure?.total?.toLocaleString() || 0"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 2: Personal Information -->
            <div x-show="currentStep === 1" class="p-6 space-y-6">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-4">Personal Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" x-model="formData.full_name" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Father's Name <span class="text-red-500">*</span></label>
                        <input type="text" name="father_name" x-model="formData.father_name" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">CNIC/B-Form <span class="text-red-500">*</span></label>
                        <input type="text" name="cnic" x-model="formData.cnic" required placeholder="XXXXX-XXXXXXX-X"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Date of Birth</label>
                        <input type="date" name="date_of_birth" x-model="formData.date_of_birth"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Gender</label>
                        <select name="gender" x-model="formData.gender"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Phone <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" x-model="formData.phone" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" x-model="formData.email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">City</label>
                        <input type="text" name="city" x-model="formData.city"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Address</label>
                        <textarea name="address" x-model="formData.address" rows="2"
                                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Step 3: Guardian Information -->
            <div x-show="currentStep === 2" class="p-6 space-y-6">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-4">Guardian Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Guardian Name <span class="text-red-500">*</span></label>
                        <input type="text" name="guardian_name" x-model="formData.guardian_name" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Relation</label>
                        <select name="guardian_relation" x-model="formData.guardian_relation"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            <option value="father">Father</option>
                            <option value="mother">Mother</option>
                            <option value="guardian">Guardian</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Guardian Phone <span class="text-red-500">*</span></label>
                        <input type="tel" name="guardian_phone" x-model="formData.guardian_phone" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Guardian CNIC</label>
                        <input type="text" name="guardian_cnic" x-model="formData.guardian_cnic"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Occupation</label>
                        <input type="text" name="guardian_occupation" x-model="formData.guardian_occupation"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>
            
            <!-- Step 4: Academic Information -->
            <div x-show="currentStep === 3" class="p-6 space-y-6">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-4">Academic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Last Qualification</label>
                        <select name="last_qualification" x-model="formData.last_qualification"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                            <option value="">Select</option>
                            <option value="Matric">Matric (SSC)</option>
                            <option value="Intermediate">Intermediate (HSSC)</option>
                            <option value="Bachelor">Bachelor's Degree</option>
                            <option value="Master">Master's Degree</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Institution Name</label>
                        <input type="text" name="institution" x-model="formData.institution"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Board/University</label>
                        <input type="text" name="board" x-model="formData.board"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Passing Year</label>
                        <input type="number" name="passing_year" x-model="formData.passing_year" min="1990" max="<?= date('Y') ?>"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Marks Obtained</label>
                        <input type="number" name="marks_obtained" x-model="formData.marks_obtained"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Total Marks</label>
                        <input type="number" name="total_marks" x-model="formData.total_marks"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Grade/Division</label>
                        <input type="text" name="grade" x-model="formData.grade"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>
            
            <!-- Step 5: Documents -->
            <div x-show="currentStep === 4" class="p-6 space-y-6">
                <h2 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-4">Upload Documents</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Passport Photo <span class="text-red-500">*</span></label>
                        <input type="file" name="photo" accept="image/*" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">CNIC Front <span class="text-red-500">*</span></label>
                        <input type="file" name="cnic_front" accept="image/*,.pdf" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">CNIC Back</label>
                        <input type="file" name="cnic_back" accept="image/*,.pdf"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Matric Certificate</label>
                        <input type="file" name="matric_certificate" accept="image/*,.pdf"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Intermediate Certificate</label>
                        <input type="file" name="inter_certificate" accept="image/*,.pdf"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
                </div>
                
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-200">
                    <p class="text-sm text-amber-800"><i class="fas fa-info-circle mr-2"></i>Maximum file size: 5MB. Accepted formats: JPG, PNG, PDF</p>
                </div>
            </div>
            
            <!-- Navigation Buttons -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-between">
                <button type="button" @click="prevStep()" x-show="currentStep > 0"
                        class="px-6 py-3 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-100 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Previous
                </button>
                <div x-show="currentStep === 0"></div>
                
                <button type="button" @click="nextStep()" x-show="currentStep < 4"
                        class="px-6 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-medium">
                    Next<i class="fas fa-arrow-right ml-2"></i>
                </button>
                
                <button type="submit" x-show="currentStep === 4" :disabled="submitting"
                        class="px-8 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 font-medium disabled:opacity-50">
                    <span x-show="!submitting"><i class="fas fa-paper-plane mr-2"></i>Submit Application</span>
                    <span x-show="submitting"><i class="fas fa-spinner fa-spin mr-2"></i>Submitting...</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function admissionForm() {
    return {
        currentStep: 0,
        submitting: false,
        loading: false,
        courses: [],
        feeStructure: null,
        formData: {
            campus_id: '',
            course_id: '',
            shift: 'morning',
            full_name: '',
            father_name: '',
            cnic: '',
            date_of_birth: '',
            gender: 'male',
            phone: '',
            email: '<?= htmlspecialchars($user['email'] ?? '') ?>',
            city: '',
            address: '',
            guardian_name: '',
            guardian_relation: 'father',
            guardian_phone: '',
            guardian_cnic: '',
            guardian_occupation: '',
            last_qualification: '',
            institution: '',
            board: '',
            passing_year: '',
            marks_obtained: '',
            total_marks: '',
            grade: ''
        },
        
        nextStep() {
            if (this.currentStep < 4) this.currentStep++;
        },
        
        prevStep() {
            if (this.currentStep > 0) this.currentStep--;
        },
        
        async loadCourses() {
            if (!this.formData.campus_id) {
                this.courses = [];
                return;
            }
            
            this.loading = true;
            this.courses = [];
            this.formData.course_id = '';
            this.feeStructure = null;
            
            try {
                const url = `<?= BASE_URL ?>/api/campuses/${this.formData.campus_id}/courses`;
                console.log('Loading courses from:', url);
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    throw new Error(`HTTP ${response.status}: ${errorText}`);
                }
                
                const data = await response.json();
                console.log('Received data:', data);
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                this.courses = Array.isArray(data) ? data : [];
                
                if (this.courses.length === 0) {
                    console.warn('No courses found for this campus');
                } else {
                    console.log(`Loaded ${this.courses.length} courses`);
                }
            } catch (e) {
                console.error('Failed to load courses:', e);
                alert('Failed to load courses: ' + e.message);
                this.courses = [];
            } finally {
                this.loading = false;
            }
        },
        
        async loadFeeStructure() {
            if (!this.formData.course_id || !this.formData.campus_id) {
                this.feeStructure = null;
                return;
            }
            
            try {
                const response = await fetch(`<?= BASE_URL ?>/api/fee-structure?course_id=${this.formData.course_id}&campus_id=${this.formData.campus_id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    this.feeStructure = await response.json();
                }
            } catch (e) {
                console.error('Failed to load fee structure:', e);
            }
        },
        
        // Get placeholder text for course dropdown
        getCoursePlaceholder() {
            if (this.loading) return 'Loading courses...';
            if (!this.formData.campus_id) return 'First select a campus';
            if (this.courses.length === 0) return 'No courses available';
            return 'Select Course';
        }
    }
}
</script>

