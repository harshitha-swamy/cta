<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create New Task</title>

    <link rel="stylesheet" href="/css/style.css">
     <link rel="stylesheet" type="text/css" href="https://d1jougtdqdwy1v.cloudfront.net/css/5.2.3/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="/js/SVG_Utility/svg.js" defer></script>
    <script src="/js/HTML_Utility/html.js" defer></script>


     <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://d1jougtdqdwy1v.cloudfront.net/js/5.2.3/bootstrap.bundle.min.js"></script>
</head>
<body>
    @include('header')
    <section class="dashboard-content">
        @include('sidebar')
            <div class="main-dashboard-container">
                <div class="hero-section-dashboard">
                     <section class="createCtaContainer">
                        <form id="ticketForm" method="POST" action="{{ route('task.store') }}">
                            @csrf
                            <input type="hidden" name="task_id" id="task_id">
                            <!-- backup of raw svg (kept for safety/traceability) -->
                            <input type="hidden" id="svg_backup" name="svg_backup" value="">
                            <h3>Create CTA</h3>
                                <div class="accordion" id="accordionExample">
                                    
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                             <span class="edit-container">
                                                <span>Task Information</span>
                                                <span class="editCtaContainer" style="display: none;"><a href="#" class="editCta"><img src="images/pencil-square.png" alt=""> &nbsp; Edit</a></span>
                                            </span>
                                        </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                
                                                
                                                <div class="createTaskInfo">
                                                    <div class="form-group-control">
                                                        <label for="ticket_link">Ticket Link</label>
                                                        <input type="url" class="form-control taskField" name="ticket_link" id="ticket_link" placeholder="Enter Ticket Link" pattern="https?://.+" title="Enter a valid URL (must start with http:// or https://)"  value="{{ old('ticket_link', $ticket->ticket_link ?? '') }}"  required class="editField form-control">
                                                    </div>
                                                    <div class="form-group-control">
                                                      <label for="task_desc">Task Description</label>
                                                      <textarea 
                                                          class="form-control taskField editField" 
                                                          id="task_desc" 
                                                          name="ticket_description" 
                                                          rows="4" 
                                                          placeholder="Enter Description" 
                                                          required
                                                      >{{ old('ticket_description', $ticket->ticket_description ?? '') }}</textarea>
                                                  </div>

                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                        <div class="form-group-control">
                                                                <label for="DealerCode">Dealer Code</label>
                                                                <input type="text" class="form-control editField taskField" id="DealerCode" name="dealer_code" placeholder="Enter Dealer Code" value="{{ old('dealer_code', $ticket->dealer_code ?? '') }}" required>
                                                            </div>  
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="form-group-control">
                                                                <label for="website">Dealer Website Link</label>
                                                                <input type="url" class="form-control editField taskField" id="website" name="website_link" placeholder="Auto-fetched Website Link" value="{{ old('website_link', $ticket->website_link ?? '') }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="form-group-control">
                                                                <div class="form-group-control">
                                                                    <label for="project">Project</label>
                                                                    @php
                                                                        $connection = session('db_connection');
                                                                        $eshopName = $connection === 'mysql2' ? 'E-Shop US' : 'E-Shop Canada';
                                                                    @endphp

                                                                    <!-- <select class="form-select editField taskField" id="project" name="project_name" required editField>
                                                                        <option value="E-Shop US" {{ $eshopName === 'E-Shop US' ? 'selected' : '' }}>E-Shop US</option>
                                                                        <option value="E-Shop Canada" {{ $eshopName === 'E-Shop Canada' ? 'selected' : '' }}>E-Shop Canada</option>
                                                                    </select> -->
                                                                    <select class="form-select editField taskField" id="project" name="project_name" required>
                                                                      <option value="E-Shop US"
                                                                          {{ old('project_name', $ticket->project_name ?? $eshopName) == 'E-Shop US' ? 'selected' : '' }}>
                                                                          E-Shop US
                                                                      </option>

                                                                      <option value="E-Shop Canada"
                                                                          {{ old('project_name', $ticket->project_name ?? $eshopName) == 'E-Shop Canada' ? 'selected' : '' }}>
                                                                          E-Shop Canada
                                                                      </option>
                                                                  </select>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                           <div class="task-section" style="{{ isset($action) ? 'display:none;' : 'display:block;' }}">
    <div class="footer-btns">
        <a href="{{ route('dashboard') }}" class="cancel-cta btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="save-cta btn btn-primary" id="saveTask">Save Task</button>
    </div>
</div>

                            
                        </form>
                        </section>

                        <!-- TAB CONTAINER -->
                        <section class="customizeContainer createCtaContainer" style="display: none;">
                            <ul class="nav nav-tabs options-tab" id="myTabAuto" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link standard active" id="new-tab-svg" data-bs-toggle="tab" data-bs-target="#new-tab-svg-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">SVG Button</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link custom" id="new-tab-png" data-bs-toggle="tab" data-bs-target="#new-tab-png-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">PNG Button</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link custom" id="new-tab-html" data-bs-toggle="tab" data-bs-target="#new-tab-html-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">HTML Code</button>
                                </li>
                            </ul>

                             <!-- TAB CONTENT START -->
                              <div class="row">
                                <div class="tab-content acc-tab-content" id="myTabAutoContent">
                                    <div class="tab-pane fade show active" id="new-tab-svg-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                                        
                                        <!-- CUSTOM UI -->
                                          <div class="row">
                                               <div class="col-lg-6">
                                                <div class="tab-contents-cta">
                                                    <h5>Upload a SVG</h5>
                                                    <div class="file-upload-wrapper">
                                                        <label for="svgUpload" class="file-upload-box">
                                                            <div class="upload-icon">
                                                            <img src="images/upload-cloud.png" alt="Upload">
                                                            </div>
                                                            <div class="upload-text">
                                                            <p>Choose a SVG File</p>
                                                            </div>
                                                            <input type="file" id="svgUpload" class="file-input" accept=".jpg,.png,.svg,.webp">
                                                        </label>

                                                        <div id="uploadedFile" class="uploaded-file"></div>
                                                    </div>
                                                      
                                                   
                                                    
                                                    <div class="editedSection">
                                                        <button class="mode-btn active" onclick="setMode('edit', this)"><i class="bi bi-pencil-fill"></i> Edit Mode</button>
                                                        <button class="mode-btn erase-mode" onclick="setMode('erase', this)"><i class="bi bi-eraser-fill"></i> Erase Mode</button>
                                                        <label for="textMode">Text Mode</label>
                                                        <select id="textMode">
                                                            <option value="single">Single Line</option>
                                                            <option value="multi">Multiline</option>
                                                        </select>
                                                        <label for="textContent" class="d-block">Button Text</label>
                                                        <input type="text" id="textContent" class="form-control editField" value="">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <label style="font-size: 12px;">X Position (%)</label>
                                                                    <input class="form-control editField" type="number" id="textX" value="50" min="0" max="100">
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label style="font-size: 12px;">Y Position (%)</label>
                                                                    <input class="form-control editField" type="number" id="textY" value="50" min="0" max="100">
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label for="textSize">Text Size</label>
                                                                    <input class="form-control editField" type="number" id="textSize" value="24" min="12" max="72"/>   
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label for="fontFamily">Font Family</label>
                                                                     <select class="form-select editField" id="fontFamily">
                                                                        <!-- System Fonts -->
                                                                        <optgroup label="System Fonts">
                                                                        <option value="Arial, sans-serif">Arial</option>
                                                                        <option value="'Times New Roman', serif">Times New Roman</option>
                                                                        <option value="Georgia, serif">Georgia</option>
                                                                        <option value="'Courier New', monospace">Courier New</option>
                                                                        <option value="Verdana, sans-serif">Verdana</option>
                                                                        <option value="'Comic Sans MS', cursive">Comic Sans MS</option>
                                                                        </optgroup>
                                                                        <!-- Google Fonts -->
                                                                        <optgroup label="Google Fonts">
                                                                        <option value="'Roboto', sans-serif">Roboto</option>
                                                                        <option value="'Open Sans', sans-serif">Open Sans</option>
                                                                        <option value="'Playfair Display', serif">Playfair Display</option>
                                                                        <option value="'Montserrat', sans-serif">Montserrat</option>
                                                                        <option value="'Lato', sans-serif">Lato</option>
                                                                        <option value="'Poppins', sans-serif">Poppins</option>
                                                                        <option value="'Merriweather', serif">Merriweather</option>
                                                                        <option value="'Source Code Pro', monospace">Source Code Pro</option>
                                                                        <option value="'Raleway', sans-serif">Raleway</option>
                                                                        </optgroup>
                                                                    </select> 
                                                                </div>
                                                            </div>                   
                                        
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <label for="fontWeight">Font Weight</label>
                                                                    <select class="form-select editField" id="fontWeight">
                                                                        <option value="400">Normal (400)</option>
                                                                        <option value="700">Bold (700)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label for="fontStyle">Font Style</label>
                                                                    <select class="form-select editField" id="fontStyle">
                                                                        <option value="normal">Normal</option>
                                                                        <option value="italic">Italic</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label for="letterSpacing">Letter Spacing (px)</label>
                                                                    <input class="form-control editField" type="number" id="letterSpacing" value="0" min="-5" max="10" step="0.5"/> 
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label for="lineHeight">Line Height (multiplier)</label>
                                                                    <input class="form-control editField" type="number" id="lineHeight" value="1.2" min="0.5" max="3" step="0.1"/>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label>Text Color</label>
                                                                    <div class="color-group">
                                                                        <input type="color" id="textColorPicker" value="#1e293b" placeholder="#1e293b"/>
                                                                        <input type="text" class="form-control editField" id="textColorText" value="#1e293b" placeholder="#1e293b"/>
                                                                    </div>
                                                                </div>
                                                               <div class="col-lg-6">
                                                                    <label>Width (px)</label>
                                                                    <input class="form-control editField" type="number" id="widthInput" value="500" min="249" max="1000" placeholder="249"> 
                                                               </div>
                                                               <div class="col-lg-6">
                                                                    <label>Height (px)</label>
                                                                    <input class="form-control editField" type="number" id="heightInput" value="500" min="64" max="1000" placeholder="64"> 
                                                               </div>
                                                               <div class="col-lg-6">
                                                                    <label>Background</label>
                                                                   <div class="color-group">
                                                                        <input type="color" id="bgColorPicker" value="#ffffff" placeholder="#6366f1"/>
                                                                        <input class="form-control editField" type="text" id="bgColorText" value="#ffffff" placeholder="#6366f1"/>
                                                                    </div>  
                                                               </div>
                                                               <div class="col-lg-6">
                                                                    <label>Border Color</label>
                                                                    <div class="color-group">
                                                                        <input type="color" id="borderColorPicker" value="#667eea" placeholder="#667eea"/>
                                                                        <input type="text" class="form-control editField" id="borderColorText" value="#667eea" placeholder="#667eea"/>
                                                                    </div>
                                                               </div>
                                                               <div class="col-lg-6">
                                                                    <label>Border Width</label>
                                                                    <input type="number" class="form-control editField" id="borderWidth" value="3" min="0" max="20" placeholder="5">  
                                                               </div>
                                                               <div class="col-lg-6">
                                                                    <label>Border Radius</label>
                                                                    <input type="number" class="form-control editField" id="borderRadius" value="10" min="0" max="100" placeholder="10"/>
                                                               </div>
                                                                <div class="col-lg-6">
                                                                    <label for="languageSelect">Translate To</label>
                                                                    <select class="form-select" id="languageSelect">
                                                                        <option value="en">English</option>
                                                                        <option value="fr-CA">Canadian French</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="add-icon-section">
                                                                <h3><i class="bi bi-file-image"></i> Add Icon</h3>
                                                                <h4>Upload Image</h4>
                                                                <input type="file" id="iconUpload" accept="image/*" multiple />
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <label for="iconX">Default X (px)</label>
                                                                        <input class="form-control editField" type="number" id="iconX" value="50"/>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <label for="iconY">Default Y (px)</label>
                                                                        <input class="form-control editField" type="number" id="iconY" value="50"/>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                         <label for="iconSize">Default Width (px)</label>
                                                                        <input type="number" class="form-control editField" id="iconSize" value="50" min="16" max="2000"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                             <div class="section">
                                                                <div class="section-title"><i class="fas fa-list"></i> Elements</div>
                                                                <div class="element-list" id="elementList">
                                                                    <p style="text-align:center; color:#94a3b8; font-size:0.8rem;">No elements added</p>
                                                                </div>
                                                            </div>
                                                            <div class="btn-section">
                                                                <button class="applyCta" onclick="applyChanges()">Apply Changes</button>
                                                                <button class="btn btn-danger" onclick="clearAllElements()">Clear All Elements</button>
                                                                <button class="download_cta" onclick="downloadSVG()">Download SVG</button>
                                                            </div>
                                                    </div>
                                                </div>
                                               </div>
                                               <div class="col-lg-6">
                                                  <div class="tab-contents-cta">
                                                    <div class="previewSection">
                                                        <div class="uploaded-file" id="referenceCtaPreview">
                                                            <p class="uploadedText">Reference CTA</p>
                                                            <div class="uploadedContainer">
                                                                <div class="uploadedImg">
                                                                  <!-- Display uploaded SVG here (reference, not editable) -->
                                                                  <div id="referenceCtaSvgContainer"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                       <div class="previewSection">
                                                         <p class="uploadedText">Generated CTA</p>
                                                         <div class="uploadedContainer">
                                                            <div class="uploaded-file" id="svgPreview">               
                                                                    
                                                                        <div class="uploadedImg">
                                                                            <svg width="400" height="100" style="">
                                                                                <rect width="100%" height="100%" fill="#fff"/>
                                                                                <text x="50%" y="50%" text-anchor="middle" font-size="24" fill="#333">
                                                                                    Upload an SVG to begin
                                                                                </text>
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                            </div>  
                                                        </div> 
                                                        
                                                        <!-- French translation copy (hidden by default) -->
                          <!-- <div id="frenchCtaBox" class="cta-box" style="display:none;">
                        <h3>French Translation CTA <span style="font-size:0.8rem; color:#64748b; font-weight:500;">(translated preview)</span></h3> -->
                        <!-- <div id="svgFrenchPreview" class="reference-preview"> -->
                          <!-- translated clone of Generated CTA will be inserted here when French is selected -->
                        <!-- </div>
                      </div> -->
                                                  </div> 
                                                  <div class="footerCta">
                                                    <button type="button" class="saveCta">Send for Approval</button>
                                                    @if(isset($ticket))
                                                    <button type="button" class="reviewCta" data-url="{{ $ticket->website_link }}">Go to Review</button>
                                                    @endif
                                                </div>
                                               </div> 
                                          </div>  
                                        <!-- CUSTOM UI  -->

                                    </div>
                                    <div class="tab-pane fade" id="new-tab-png-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                                      <p>PNG</p>
                                    </div>
                                    <div class="tab-pane fade" id="new-tab-html-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                                         
                                        <!-- CUSTOM UI -->
                                          <div class="row">
                                               <div class="col-lg-6">
                                                <div class="tab-contents-cta">
                                                    <h5>Upload a SVG</h5>
                                                    <button type="button" class="sampleBtn" onclick="html_loadDefaultSVG()">Edit Sample SVG</button>
                                                    
                                                    <div class="editedSection">
                                                        <button class="mode-btn active" onclick="html_setMode('edit', this)"><i class="bi bi-pencil-fill"></i> Edit Mode</button>
                                                        <button class="mode-btn erase-mode" onclick="html_setMode('erase', this)"><i class="bi bi-eraser-fill"></i> Erase Mode</button>
                                                        <label for="html_textMode">Text Mode</label>
                                                        <select id="html_textMode">
                                                            <option value="single">Single Line</option>
                                                            <option value="multi">Multiline</option>
                                                        </select>
                                                        <label for="html_textContent" class="d-block">Button Text</label>
                                                        <input type="text" id="html_textContent" class="form-control editField" value="">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <label style="font-size: 12px;">X Position (%)</label>
                                                                    <input class="form-control editField" type="number" id="html_textX" value="50" min="0" max="100">
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label style="font-size: 12px;">Y Position (%)</label>
                                                                    <input class="form-control editField" type="number" id="html_textY" value="50" min="0" max="100">
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label for="html_textSize">Text Size</label>
                                                                    <input class="form-control editField" type="number" id="html_textSize" value="24" min="12" max="72"/>   
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label for="html_fontFamily">Font Family</label>
                                                                     <select class="form-select editField" id="html_fontFamily">
                                                                        <!-- System Fonts -->
                                                                        <optgroup label="System Fonts">
                                                                        <option value="Arial, sans-serif">Arial</option>
                                                                        <option value="'Times New Roman', serif">Times New Roman</option>
                                                                        <option value="Georgia, serif">Georgia</option>
                                                                        <option value="'Courier New', monospace">Courier New</option>
                                                                        <option value="Verdana, sans-serif">Verdana</option>
                                                                        <option value="'Comic Sans MS', cursive">Comic Sans MS</option>
                                                                        </optgroup>
                                                                        <!-- Google Fonts -->
                                                                        <optgroup label="Google Fonts">
                                                                        <option value="'Roboto', sans-serif">Roboto</option>
                                                                        <option value="'Open Sans', sans-serif">Open Sans</option>
                                                                        <option value="'Playfair Display', serif">Playfair Display</option>
                                                                        <option value="'Montserrat', sans-serif">Montserrat</option>
                                                                        <option value="'Lato', sans-serif">Lato</option>
                                                                        <option value="'Poppins', sans-serif">Poppins</option>
                                                                        <option value="'Merriweather', serif">Merriweather</option>
                                                                        <option value="'Source Code Pro', monospace">Source Code Pro</option>
                                                                        <option value="'Raleway', sans-serif">Raleway</option>
                                                                        </optgroup>
                                                                    </select> 
                                                                </div>
                                                            </div>                   
                                        
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <label for="html_fontWeight">Font Weight</label>
                                                                    <select class="form-select editField" id="html_fontWeight">
                                                                        <option value="400">Normal (400)</option>
                                                                        <option value="700">Bold (700)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label for="html_fontStyle">Font Style</label>
                                                                    <select class="form-select editField" id="html_fontStyle">
                                                                        <option value="normal">Normal</option>
                                                                        <option value="italic">Italic</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label for="html_letterSpacing">Letter Spacing (px)</label>
                                                                    <input class="form-control editField" type="number" id="html_letterSpacing" value="0" min="-5" max="10" step="0.5"/> 
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label for="html_lineHeight">Line Height (multiplier)</label>
                                                                    <input class="form-control editField" type="number" id="html_lineHeight" value="1.2" min="0.5" max="3" step="0.1"/>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label>Text Color</label>
                                                                    <div class="color-group">
                                                                        <input type="color" id="html_textColorPicker" value="#1e293b" placeholder="#1e293b"/>
                                                                        <input type="text" class="form-control editField" id="html_textColorText" value="#1e293b" placeholder="#1e293b"/>
                                                                    </div>
                                                                </div>
                                                               <div class="col-lg-6">
                                                                    <label>Width (px)</label>
                                                                    <input class="form-control editField" type="number" id="html_widthInput" value="249" min="249" max="1000" placeholder="249"> 
                                                               </div>
                                                               <div class="col-lg-6">
                                                                    <label>Height (px)</label>
                                                                    <input class="form-control editField" type="number" id="html_heightInput" value="64" min="64" max="1000" placeholder="64"> 
                                                               </div>
                                                               <div class="col-lg-6">
                                                                    <label>Background</label>
                                                                   <div class="color-group">
                                                                        <input type="color" id="html_bgColorPicker" value="#ffffff" placeholder="#6366f1"/>
                                                                        <input class="form-control editField" type="text" id="html_bgColorText" value="#ffffff" placeholder="#6366f1"/>
                                                                    </div>  
                                                               </div>
                                                               <div class="col-lg-6">
                                                                    <label>Border Color</label>
                                                                    <div class="color-group">
                                                                        <input type="color" id="html_borderColorPicker" value="#667eea" placeholder="#667eea"/>
                                                                        <input type="text" class="form-control editField" id="html_borderColorText" value="#667eea" placeholder="#667eea"/>
                                                                    </div>
                                                               </div>
                                                               <div class="col-lg-6">
                                                                    <label>Border Width</label>
                                                                    <input type="number" class="form-control editField" id="html_borderWidth" value="3" min="0" max="20" placeholder="5">  
                                                               </div>
                                                               <div class="col-lg-6">
                                                                    <label>Border Radius</label>
                                                                    <input type="number" class="form-control editField" id="html_borderRadius" value="10" min="0" max="100" placeholder="10"/>
                                                               </div>
                                                                <div class="col-lg-6">
                                                                    <label for="html_languageSelect">Translate To</label>
                                                                    <select class="form-select" id="html_languageSelect">
                                                                        <option value="en">English</option>
                                                                        <option value="fr-CA">Canadian French</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="add-icon-section">
                                                                <h3><i class="bi bi-file-image"></i> Add Icon</h3>
                                                                <h4>Upload Image</h4>
                                                                <input type="file" id="html_iconUpload" accept="image/*" multiple />
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <label for="html_iconX">Default X (px)</label>
                                                                        <input class="form-control editField" type="number" id="html_iconX" value="50"/>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <label for="html_iconY">Default Y (px)</label>
                                                                        <input class="form-control editField" type="number" id="html_iconY" value="50"/>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                         <label for="html_iconSize">Default Width (px)</label>
                                                                        <input type="number" class="form-control editField" id="html_iconSize" value="50" min="16" max="2000"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                             <div class="section">
                                                                <div class="section-title"><i class="fas fa-list"></i> Elements</div>
                                                                <div class="element-list" id="html_elementList">
                                                                    <p style="text-align:center; color:#94a3b8; font-size:0.8rem;">No elements added</p>
                                                                </div>
                                                            </div>
                                                            <div class="btn-section">
                                                                <button class="applyCta" onclick="html_applyChanges()">Apply Changes</button>
                                                                <button class="btn btn-danger" onclick="html_clearAllElements()">Clear All Elements</button>
                                                                <button class="downloadCTA" onclick="html_downloadSVG()">Download SVG</button>
                                                            </div>
                                                    </div>
                                                </div>
                                               </div>
                                               <div class="col-lg-6">
                                                  <div class="tab-contents-cta">
                                                       <div class="previewSection">
                                                            <div class="uploaded-file" id="html_svgPreview">
                                                                    <p class="uploadedText">Uploaded File</p>
                                                                    <div class="uploadedContainer">
                                                                        <div class="uploadedImg">
                                                                            <svg width="400" height="100">
                                                                                <rect width="100%" height="100%" fill="#fff"/>
                                                                                <text x="50%" y="50%" text-anchor="middle" font-size="24" fill="#333">
                                                                                    Upload an SVG to begin
                                                                                </text>
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                            </div>  
                                                        </div>  
                                                  </div> 
                                                  <div class="footerCta">
                                                    <button type="button" class="html_saveCta">Send for Approval</button>
                                                    @if(isset($ticket))
                                                    <button type="button" class="html_reviewCta" data-url="{{ $ticket->website_link }}">Go to Review</button>
                                                    @endif
                                                </div>
                                               </div> 
                                          </div>  
                                        <!-- CUSTOM UI  -->
                                    </div>
                                </div>
                          </div>
                        <!-- TAB CONTENT START -->

                        </section>
                        <!-- TAB CONTAINER -->
                </div>
            </div>
    </section>
                                                          
    
    

    <!-- form footer buttons hide  -->
@if(isset($action))                      
  @if($action == 'edit')
    <script>   
    $(document).ready(function() {
        document.addEventListener("DOMContentLoaded", function() {
          $('.editCtaContainer').css('display', 'block');
        });
        $('.editCtaContainer').show();     
        $('.task-section').hide();   
        $('.createCtaContainer').show();  
        $('.createTaskInfo').addClass('cta-event-disable');
    });
    </script>
  @endif
@endif
<!--end of form footer buttons hide  -->
</body>
</html>


<script>
  $(document).on('click', '.reviewCta', function () {
    let url = $(this).data('url');
    if (!url) {
        alert("No website link available!");
        return;
    }
    window.open(url, '_blank');
  });


$(function() {
    const dbConnection = "{{ session('db_connection') }}"; // mysql2 = US, mysql = CA

    $('#DealerCode').on('input', function(e) {
        
        let val = $(this).val();

        if (dbConnection === 'mysql2') {
            val = val.replace(/\D/g, '');
            $(this).val(val);

            if (val.length === 5) {
                fetchWebsiteLink(val);
            }
        } else if (dbConnection === 'mysql') {
            val = val.replace(/[^a-zA-Z0-9]/g, '');
            $(this).val(val);

            if (val.length === 6) {
                fetchWebsiteLink(val);
            }
        }
    });

    // Function to fetch website link from backend
    function fetchWebsiteLink(dealerCode) {
        
        $.ajax({
            url: "{{ route('get.website.link') }}", 
            type: "GET",
            data: { dealer_code: dealerCode },
            success: function(response) {
                if (response.website_link) {
                    $('#website').val(response.website_link);
                } else {
                    $('#website').val('');
                    alert('No website link found for this dealer.');
                }
            },
            error: function() {
                alert('Error fetching website link.');
            }
        });
    }
});


$(document).ready(function() {
 $('#ticketForm').on('submit', function(e) {
    e.preventDefault();

    let form = $(this);
    let formData = form.serialize();
    let saveBtn = $('#saveTask');

    saveBtn.prop('disabled', true).html('Saving...');

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: formData,
        success: function(res) {
            if (res.success) {
                alert(res.message); 
                $('.createTaskInfo').addClass('cta-event-disable');
                $('.editCtaContainer').show();
                $('.task-section').hide();
                if (res.task_id) {
                    $('#task_id').val(res.task_id);
                }
                $('.createCtaContainer').show();
            }
            saveBtn.prop('disabled', false).html('Save Task');
        },
        error: function(xhr) {
            saveBtn.prop('disabled', false).html('Save Task');

            if (xhr.status === 409) {
                alert(xhr.responseJSON.message);
                return;
            }

            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let msg = '';
                $.each(errors, function(key, value) {
                    msg += value + '\n';
                });
                alert(msg);
            }
        }
    });
});


    $(document).on('click', '.editCta', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        $('.createTaskInfo').removeClass('cta-event-disable');
        $('.customizeContainer').hide();
        $('.task-section').show();
    });

});

  


$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

  $(document).on('click', '.saveCta', function () {
    const svgEl = document.querySelector('#svgPreview svg');
    if (!svgEl) { alert('No SVG to save! Please upload or create a SVG first.'); return; }

    // create raw backup (original DOM SVG markup)
    $('#svg_backup').val(svgEl.outerHTML);

    // build exported source that matches the downloadable SVG
    const exportedSource = exportSVGSource();
    if (!exportedSource) { alert('Unable to create export SVG.'); return; }

    const blob = new Blob([exportedSource], { type: 'image/svg+xml;charset=utf-8' });

    const formData = new FormData();
    formData.append('dealer_code', $('#DealerCode').val() || '');
    formData.append('button_text', $('#textContent').val() || '');
    // append CSRF token to FormData as an explicit fallback
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    // server side will receive 'svg_file' as the exported svg file (same content as download)
    formData.append('svg_file', blob, 'edited-svg.svg');
    // include serialized svg string as 'svg' so server-side validation passes
    formData.append('svg', exportedSource);
    // also include raw SVG backup for traceability
    formData.append('svg_backup', $('#svg_backup').val());

    $.ajax({
        url: "/upload-svg-temp",
        method: "POST",
        processData: false,
        contentType: false,
        data: formData,
        success: function (res) {
            alert("SVG saved locally!");
            // populate inputs if API returns file_url (backwards-compatible)
            if (res && res.file_url) {
                $('#button_image_url').val(res.file_url);
                $('#button_image_url_vdp').val(res.file_url);
                $('#button_image_url_cpov').val(res.file_url);
                $('#preview_button_image_url').attr('href', res.file_url);
                $('#preview_button_image_url_vdp').attr('href', res.file_url);
                $('#preview_button_image_url_cpov').attr('href', res.file_url);
            }
        },
        error: function (err) {
            console.error(err);
            alert("Save failed");
        }
    });
  });


  // button image upload-->
  $(document).on('click', '.html_saveCta', function () {
    const svgEl = document.querySelector('#svgPreview svg');
    if (!svgEl) { alert('No SVG to save! Please upload or create a SVG first.'); return; }

    // create raw backup (original DOM SVG markup)
    $('#svg_backup').val(svgEl.outerHTML);

    // build exported source that matches the downloadable SVG
    const exportedSource = exportSVGSource();
    if (!exportedSource) { alert('Unable to create export SVG.'); return; }

    const blob = new Blob([exportedSource], { type: 'image/svg+xml;charset=utf-8' });

    const formData = new FormData();
    formData.append('dealer_code', $('#DealerCode').val() || '');
    formData.append('button_text', $('#html_textContent').val() || '');
    // append CSRF token to FormData as an explicit fallback
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    // server side will receive 'svg_file' as the exported svg file (same content as download)
    formData.append('svg_file', blob, 'edited-svg.svg');
    // include serialized svg string as 'svg' so server-side validation passes
    formData.append('svg', exportedSource);
    // also include raw SVG backup for traceability
    formData.append('svg_backup', $('#svg_backup').val());

    $.ajax({
        url: "/upload-svg-temp",
        method: "POST",
        processData: false,
        contentType: false,
        data: formData,
        success: function (res) {
            alert("SVG saved locally!");
            // populate inputs if API returns file_url (backwards-compatible)
            if (res && res.file_url) {
                $('#button_image_url').val(res.file_url);
                $('#button_image_url_vdp').val(res.file_url);
                $('#button_image_url_cpov').val(res.file_url);
                $('#preview_button_image_url').attr('href', res.file_url);
                $('#preview_button_image_url_vdp').attr('href', res.file_url);
                $('#preview_button_image_url_cpov').attr('href', res.file_url);
            }
        },
        error: function (err) {
            console.error(err);
            alert("Save failed");
        }
    });
  });

  $(document).on('click', '.html_reviewCta', function () {
    let url = $(this).data('url');
    if (!url) {
        alert("No website link available!");
        return;
    }
    window.open(url, '_blank');
  });
</script>











//html----------------------------
<script>
  



    // BUTTON TOGGLE FOR TEXT PREVIEW MODES
    //  SVG UTILITY FUNCTIONS
    let html_currentSVG = null;
    let html_selectedIcon = '';
    let html_currentMode = 'edit';
    let html_addedElements = [];
    let html_originalTexts = new WeakMap();
    let html_dragged = null;
    let html_offset = { x: 0, y: 0 };
    let html_currentViewBox = { w: 249, h: 64 };
    let html_selectedElement = null;
    let html_selectionBox = null;


     window.html_loadDefaultSVG = function() {
  const sample = `
    <svg id="html_svgPreview-1" xmlns="http://www.w3.org/2000/svg" width="249" height="64" viewBox="0 0 249 64">
      <rect width="249" height="64" fill="#ffffff" data-background="true"/>
      <text x="50%" y="50%" text-anchor="middle" dominant-baseline="middle" font-size="14" fill="#0f172a">Sample</text>
    </svg>`;
  html_currentSVG = sample;

  html_displaySVG();
  html_applyTranslation();
};
    // const translations = {
    //   'en': {},
    //   'fr-CA': {
    //     'hello': 'Bonjour',
    //     'welcome': 'Bienvenue',
    //     'thank you': 'Merci',
    //     'yes': 'Oui',
    //     'no': 'Non',
    //     'upload an svg to begin': 'Téléchargez un SVG pour commencer',
    //     'edit': 'Éditer',
    //     'erase': 'Effacer'
    //   }
    // };

    // ---------- FILE UPLOAD (SVG) ----------
    // document.getElementById('svgUpload').addEventListener('change', function (e) {
    //   const file = e.target.files[0];
    //   if (file && file.type === 'image/svg+xml') {
    //     const reader = new FileReader();
    //     reader.onload = async function (event) {
    //       currentSVG = event.target.result;
    //       displaySVG();
    //       await applyTranslation();
    //       document.getElementById('referenceCtaSvgContainer').innerHTML = event.target.result;
    //     };
    //     reader.readAsText(file);
    //   }
    // });

    // ---------- COLOR SYNC ----------
    document.getElementById('html_borderColorPicker').addEventListener('input', e => {
      document.getElementById('html_borderColorText').value = e.target.value;
      // update live visuals when user uses the color picker
      try { html_applyVisualStyles(); } catch (err) {}
    });
    document.getElementById('html_borderColorText').addEventListener('input', e => {
      document.getElementById('html_borderColorPicker').value = e.target.value;
      try { html_applyVisualStyles(); } catch (err) {}
    });
    document.getElementById('html_textColorPicker').addEventListener('input', e => {
      document.getElementById('html_textColorText').value = e.target.value;
      try { html_updatePreviewText(); } catch (err) {}
    });
    document.getElementById('html_textColorText').addEventListener('input', e => {
      document.getElementById('html_textColorPicker').value = e.target.value;
      try { html_updatePreviewText(); } catch (err) {}
    });
    document.getElementById('html_bgColorPicker').addEventListener('input', e => {
      document.getElementById('html_bgColorText').value = e.target.value;
      try { html_applyVisualStyles(); } catch (err) {}
    });
    document.getElementById('html_bgColorText').addEventListener('input', e => {
      document.getElementById('html_bgColorPicker').value = e.target.value;
      try { html_applyVisualStyles(); } catch (err) {}
    });

    // ---------- ICON IMAGE UPLOAD (multiple images, default placement, draggable) ----------
    // We'll accept multiple files from #iconUpload and insert each as an <image> into the current svg.
    const html_uploadInput = document.getElementById("html_iconUpload");
    const html_iconX = document.getElementById("html_iconX");
    const html_iconY = document.getElementById("html_iconY");
    const html_iconSize = document.getElementById("html_iconSize");

    html_uploadInput.addEventListener("change", function () {
        const files = Array.from(this.files || []);
        if (!files.length) return;

        // Insert each file
        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                const base64 = e.target.result;
                html_insertUploadedImage(base64, file.name);
            };
            reader.readAsDataURL(file);
        });

        // clear the input so same file can be picked again if needed
        this.value = '';
    });

    // Insert image into current SVG with default px placement, convert to viewBox units
    // function html_insertUploadedImage(base64url, filename) {
    //     const svg = document.querySelector('#html_svgPreview svg');
    //     if (!svg) {
    //         console.error('No SVG available to insert image into.');
    //         return;
    //     }

    //     // Ensure xlink namespace exists
    //     if (!svg.getAttribute('xmlns:xlink')) svg.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');

    //     // default pixel placement (Option A)
    //     const defaultXpx = parseFloat(html_iconX.value) || 50;   // we still respect the default inputs if user changed them
    //     const defaultYpx = parseFloat(html_iconY.value) || 50;
    //     const defaultWpx = parseFloat(html_iconSize.value) || 50;

    //     // Convert px to viewBox units
    //     const viewBox = svg.getAttribute('viewBox').split(/\s+/).map(Number);
    //     const vbWidth = viewBox[2];
    //     const vbHeight = viewBox[3];

    //     // container (visible) pixel size
    //     // Use the SVG's actual rendered pixel size to compute scale so
    //     // editor chrome (borders) applied to the container don't change
    //     // the conversion from px -> viewBox units.
    //     const svgRect = svg.getBoundingClientRect();
    //     const containerW = svgRect.width || svg.parentElement.clientWidth || vbWidth;
    //     const containerH = svgRect.height || svg.parentElement.clientHeight || vbHeight;

    //     // scale to convert px -> viewBox units (use width-based scale)
    //     const scale = vbWidth / (containerW || vbWidth);

    //     const x_vb = defaultXpx * scale;
    //     const y_vb = defaultYpx * scale;
    //     const w_vb = defaultWpx * scale;

    //     // Create SVG <image>
    //     const imgEl = document.createElementNS('http://www.w3.org/2000/svg', 'image');
    //     imgEl.setAttributeNS('http://www.w3.org/1999/xlink', 'href', base64url);

    //     // To preserve aspect ratio, we need natural image dimensions — create an Image object
    //     const tmpImg = new Image();
    //     tmpImg.onload = function() {
    //         const natW = tmpImg.naturalWidth || 1;
    //         const natH = tmpImg.naturalHeight || 1;
    //         const aspect = natH / natW;
    //         const h_vb = w_vb * aspect;

    //         imgEl.setAttribute('x', String(x_vb));
    //         imgEl.setAttribute('y', String(y_vb));
    //         imgEl.setAttribute('width', String(w_vb));
    //         imgEl.setAttribute('height', String(h_vb));
    //         imgEl.setAttribute('preserveAspectRatio', 'xMidYMid meet');

    //         const id = 'img-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
    //         imgEl.setAttribute('data-element-id', id);

    //       // store natural size and base px width so we can update size later
    //       imgEl.setAttribute('data-natw', String(natW));
    //       imgEl.setAttribute('data-nath', String(natH));
    //       imgEl.setAttribute('data-base-width-px', String(defaultWpx));

    //         svg.appendChild(imgEl);

    //         // Record in html_addedElements list
    //         html_addedElements.push({ id, type: 'image', content: filename || 'uploaded-image' });

    //         // Make interactive
    //         html_reapplyInteractivity();
    //         html_updateElementList();
    //     };
    //     tmpImg.src = base64url;
    // }
function html_insertUploadedImage(base64url, filename) {
    const svg = document.querySelector('#html_svgPreview');
    // if (!svg || svg.tagName.toLowerCase() !== 'svg') {
    //     console.error('No SVG found');
    //     return;
    // }
    if (!svg) {
            console.error('No SVG available to insert image into.');
            return;
        }

    const defaultXpx = 50;
    const defaultYpx = 50;
    const defaultWpx = 50;

    const viewBox = svg.getAttribute('viewBox').split(/\s+/).map(Number);
    const vbWidth = viewBox[2];
    const vbHeight = viewBox[3];

    const svgRect = svg.getBoundingClientRect();
    const containerW = svgRect.width || vbWidth;
    const containerH = svgRect.height || vbHeight;
    const scale = vbWidth / containerW;

    const x_vb = defaultXpx * scale;
    const y_vb = defaultYpx * scale;
    const w_vb = defaultWpx * scale;

    const imgEl = document.createElementNS('http://www.w3.org/2000/svg', 'image');
    imgEl.setAttributeNS('http://www.w3.org/1999/xlink', 'href', base64url);

    const tmpImg = new Image();
    tmpImg.onload = function() {
        const aspect = tmpImg.naturalHeight / tmpImg.naturalWidth;
        imgEl.setAttribute('x', String(x_vb));
        imgEl.setAttribute('y', String(y_vb));
        imgEl.setAttribute('width', String(w_vb));
        imgEl.setAttribute('height', String(w_vb * aspect));
        imgEl.setAttribute('preserveAspectRatio', 'xMidYMid meet');

        const id = 'img-' + Date.now();
        imgEl.setAttribute('data-element-id', id);

        svg.appendChild(imgEl);
    };
    tmpImg.src = base64url;
}

    // ---------- LANGUAGE CHANGE ----------
    // When language changes, translate existing SVG texts AND update
    // the live textarea preview (without overwriting user input).
    document.getElementById('html_languageSelect').addEventListener('change', async () => {
      await html_applyTranslation ();
      try {
        const lang = document.getElementById('html_languageSelect').value;
        const raw = (document.getElementById('html_textContent').value || '').trim();
        if (raw) {
          const translated = await translateText(raw, lang);
          html_updatePreviewText(translated);
        }
      } catch (err) { console.error(err); }
    });

    // ---------- TEXT MODE TOGGLE ----------
    document.getElementById('html_textMode').addEventListener('change', function(e) {
      const multilineOptions = document.getElementById('html_multilineOptions');
      if (e.target.value === 'multi') {
        multilineOptions.style.display = 'block';
      } else {
        multilineOptions.style.display = 'none';
      }
    });

    // ---------- MODE SWITCH ----------
    function html_setMode(mode, btn) {
      console.log('Switching to mode:', mode);
      html_currentMode = mode;
      document.querySelectorAll('.mode-btn').forEach(btn => btn.classList.remove('active'));
      btn.classList.add('active');
      const svg = document.querySelector('#html_svgPreview svg');
      if (svg) {
        if (mode === 'erase') {
          svg.classList.add('erase-cursor');
          html_makeElementsErasable();
        } else {
          svg.classList.remove('erase-cursor');
          html_removeErasableClasses();
        }
      }
      html_reapplyInteractivity();
    }

    function loadGoogleFont(fontName) {
  // Extract just the primary font name (strip fallbacks & quotes)
  try {
    const primary = (fontName || '').split(',')[0].replace(/['"]/g, '').trim();
    if (!primary) return;

    // Map known Google font names to query-friendly family strings
    const googleFontsMap = {
      'Roboto': 'Roboto',
      'Open Sans': 'Open+Sans',
      'Playfair Display': 'Playfair+Display',
      'Montserrat': 'Montserrat',
      'Lato': 'Lato',
      'Poppins': 'Poppins',
      'Merriweather': 'Merriweather',
      'Source Code Pro': 'Source+Code+Pro',
      'Raleway': 'Raleway'
    };

    const familyQuery = googleFontsMap[primary];
    if (!familyQuery) return; // not a Google font we manage

    const linkId = `font-${primary.replace(/\s+/g, '-')}`;
    if (!document.getElementById(linkId)) {
      const link = document.createElement('link');
      link.id = linkId;
      link.rel = 'stylesheet';
      link.href = `https://fonts.googleapis.com/css2?family=${familyQuery}:wght@400;700&display=swap`;
      document.head.appendChild(link);
    }

    // Try to ensure the font is loaded and then force SVG text repaint
    // Use the Font Loading API when available.
    const fontFaceName = primary;
    if (document.fonts && document.fonts.load) {
      // Request a normal font weight first (1rem suffices)
      document.fonts.load(`1rem "${fontFaceName}"`).then(() => {
        try {
          // Re-apply font-family to SVG texts in the two preview areas to force repaint
          const applyTo = selector => {
            document.querySelectorAll(selector).forEach(el => {
              // preserve original attribute but set inline style to trigger repaint
              const current = el.getAttribute('font-family') || el.style.fontFamily || fontName;
              // set both style and attribute for broader compatibility
              el.style.fontFamily = current;
              el.setAttribute('font-family', current);
            });
          };
          applyTo('#html_svgPreview text, #html_svgPreview tspan');
          applyTo('#html_svgPreview text, #html_svgPreview tspan');
          // also update any french preview if present
          applyTo('#svgFrenchPreview text, #svgFrenchPreview tspan');
        } catch (err) {
          // noop — failing to force repaint is non-fatal
          console.error('Font apply error', err);
        }
      }).catch(() => {
        // ignore load failures — font may be unavailable but nothing else to do
      });
    }
  } catch (e) {
    // keep silent to avoid breaking existing flows
    console.error('loadGoogleFont error', e);
  }
}

    // ---------- DISPLAY SVG ----------
    function html_displaySVG() {
      if (!html_currentSVG) return;

      const parser = new DOMParser();
      const svgDoc = parser.parseFromString(html_currentSVG, 'image/svg+xml');
      const svgElement = svgDoc.documentElement;
      alert("SVG Loaded Successfully!");

      // Ensure xlink namespace exists on the inserted SVG
      if (!svgElement.getAttribute('xmlns:xlink')) svgElement.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');

      const preview = document.getElementById('html_svgPreview');
      function getNaturalSize(el, container) {
        const vbAttr = el.getAttribute('viewBox');
        if (vbAttr) {
          const parts = vbAttr.trim().split(/\s+/).map(Number);
          if (parts.length === 4 && parts[2] && parts[3]) return { w: parts[2], h: parts[3] };
        }
        const wAttr = el.getAttribute('width');
        const hAttr = el.getAttribute('height');
        const isPercent = s => typeof s === 'string' && s.trim().endsWith('%');
        if (wAttr && hAttr && !isPercent(wAttr) && !isPercent(hAttr)) {
          const wnum = parseFloat(wAttr); const hnum = parseFloat(hAttr);
          if (!Number.isNaN(wnum) && !Number.isNaN(hnum) && wnum > 0 && hnum > 0) return { w: wnum, h: hnum };
        }
        try {
          const alreadyInDom = container.contains(el);
          if (!alreadyInDom) container.appendChild(el);
          const rect = el.getBoundingClientRect();
          if (rect.width && rect.height) { if (!alreadyInDom) container.removeChild(el); return { w: Math.round(rect.width), h: Math.round(rect.height) }; }
          if (!alreadyInDom) container.removeChild(el);
        } catch (e) {}
        return { w: 400, h: 400 };
      }

      const size = getNaturalSize(svgElement, preview);
      let origW = size.w; let origH = size.h;

      document.getElementById("widthInput").value = origW;
      document.getElementById("heightInput").value = origH;

      let viewBox = svgElement.getAttribute('viewBox');
      if (!viewBox) {
        const w = svgElement.getAttribute('width') || origW || 400;
        const h = svgElement.getAttribute('height') || origH || 400;
        viewBox = `0 0 ${w} ${h}`;
        svgElement.setAttribute('viewBox', viewBox);
      }

  // Set explicit pixel width/height on the inserted SVG so the preview
  // reflects the SVG's natural coordinate system exactly and does not
  // get scaled by surrounding CSS (padding, borders, flex sizing).
  // We keep the viewBox for internal coordinates but also set physical
  // width/height to the computed natural size in pixels.
  svgElement.setAttribute('width', String(origW));
  svgElement.setAttribute('height', String(origH));
  svgElement.style.width = `${origW}px`;
  svgElement.style.height = `${origH}px`;

      // Set a normalized viewBox based on the computed natural size
      svgElement.setAttribute("viewBox", `0 0 ${origW} ${origH}`);

      preview.innerHTML = '';
      preview.appendChild(svgElement);

      // Ensure background rect
      let bgRect = svgElement.querySelector('rect[data-background="true"]');
      if (!bgRect) {
        bgRect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        bgRect.setAttribute('width', '100%');
        bgRect.setAttribute('height', '100%');
        bgRect.setAttribute('fill', document.getElementById('html_bgColorText').value);
        bgRect.setAttribute('data-background', 'true');
        svgElement.insertBefore(bgRect, svgElement.firstChild);
      }

  // Match original size (use numeric values to avoid percentage rounding)
  bgRect.setAttribute("x", 0);
  bgRect.setAttribute("y", 0);
  bgRect.setAttribute("width", String(origW));
  bgRect.setAttribute("height", String(origH));

      // Store original texts (preserve original casing) so we can recreate
      // the English/original version exactly when exporting.
      svgElement.querySelectorAll('text').forEach(text => {
        if (!html_originalTexts.has(text)) {
          html_originalTexts.set(text, text.textContent.trim());
        }
      });

      html_applyVisualStyles();
      html_reapplyInteractivity();
      html_addedElements = [];
      html_updateElementList();
      // update live preview text if user is typing
      try { html_updatePreviewText(); } catch (e) { /* ignore if preview not ready */ }
    }

    // ---------- UPDATE UPLOADED IMAGES SIZE ----------
    function html_updateUploadedImagesSize() {
      const svg = document.querySelector('#html_svgPreview svg');
      if (!svg) return;

      const images = Array.from(svg.querySelectorAll('image[data-natw]'));
      if (!images.length) return;

      const iconSizePx = parseFloat(document.getElementById('html_iconSize').value) || 50;

      // compute scale from container pixels -> viewBox units
      const viewBox = svg.getAttribute('viewBox').split(/\s+/).map(Number);
      const vbWidth = viewBox[2];

      // Use SVG's rendered rect to compute pixel->viewBox scale so
      // changes to container borders or padding don't affect icon sizing.
      const svgRect = svg.getBoundingClientRect();
      const containerW = svgRect.width || svg.parentElement.clientWidth || vbWidth;
      const scale = vbWidth / (containerW || vbWidth);

      images.forEach(img => {
        // natural aspect ratio
        const natW = parseFloat(img.getAttribute('data-natw')) || 1;
        const natH = parseFloat(img.getAttribute('data-nath')) || 1;
        const aspect = natH / natW;

        // compute new width in viewBox units from requested px width
        const w_vb = iconSizePx * scale;
        const h_vb = w_vb * aspect;

        img.setAttribute('width', String(w_vb));
        img.setAttribute('height', String(h_vb));
      });
    }

    // ---------- VISUAL STYLES ----------
    function html_applyVisualStyles() {
      const svg = document.querySelector('#html_svgPreview svg');
      if (!svg) return;

      const width = parseFloat(document.getElementById('html_widthInput').value);
      const height = parseFloat(document.getElementById('html_heightInput').value);
      const borderColor = document.getElementById('html_borderColorText').value;
      const borderWidth = parseFloat(document.getElementById('html_borderWidth').value);
      const borderRadius = parseFloat(document.getElementById('html_borderRadius').value);
      const bgColor = document.getElementById('html_bgColorText').value;

    const container = document.getElementById('html_svgPreview');
  container.style.width = `${width}px`;
  container.style.height = `${height}px`;

  // Present the editor border on the preview container so the SVG
  // stays free of editor chrome. The container will clip its
  // contents using overflow:hidden so the rounded corners look
  // correct while inner shapes keep rectangular corners.
  container.style.border = `${borderWidth}px solid ${borderColor}`;
  container.style.borderRadius = `${borderRadius}px`;
  container.style.overflow = 'hidden';

  // Update the SVG physical size to match the container but do not
  // add a CSS border on the SVG itself (avoid double borders).
  svg.setAttribute('width', String(width));
  svg.setAttribute('height', String(height));
  svg.style.width = `${width}px`;
  svg.style.height = `${height}px`;
  svg.style.border = 'none';
  svg.style.borderRadius = '0';
  svg.style.overflow = 'visible';

      const bgRect = svg.querySelector('rect[data-background="true"]');
      if (!bgRect) {
        bgRect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
        bgRect.setAttribute("data-background", "true");
        svg.insertBefore(bgRect, svg.firstChild);
      }
      
      bgRect.setAttribute("x", 0);
      bgRect.setAttribute("y", 0);
      bgRect.setAttribute("width", width);
      bgRect.setAttribute("height", height);
      bgRect.setAttribute("fill", bgColor);
      bgRect.setAttribute("rx", 0);

    
      
        const existingBorderRect = svg.querySelector('rect[data-border="true"]');
        if (existingBorderRect) existingBorderRect.remove();

}


    // ---------- TEXT WRAPPING HELPER ----------
    function wrapText(text, maxWidth, fontSize, fontFamily) {
      // Split by explicit line breaks first (user-entered newlines)
      const explicitLines = text.split('\n');
      const allLines = [];
     
      const avgCharWidth = fontSize * 0.55;
      const charsPerLine = Math.max(1, Math.floor(maxWidth / avgCharWidth));
      
      for (const line of explicitLines) {
        const trimmedLine = line.trim();
        if (trimmedLine === '') {
          allLines.push(''); // preserve empty lines
          continue;
        }
        
        // Word wrap each explicit line
        const words = trimmedLine.split(' ');
        let currentLine = '';
        
        for (const word of words) {
          const testLine = currentLine + (currentLine ? ' ' : '') + word;
          
          if (testLine.length > charsPerLine && currentLine) {
            // Current word doesn't fit, push current line and start new one
            allLines.push(currentLine.trim());
            currentLine = word;
          } else {
            // Word fits, add it to current line
            currentLine = testLine;
          }
        }
        
        if (currentLine.trim()) {
          allLines.push(currentLine.trim());
        }
      }
      
      return allLines.length > 0 ? allLines : [''];
    }

    // ---------- TRANSLATION HELPERS ----------
    async function translateText(original, targetLang) {
      if (!original) return '';

      // Normalize whitespace and casing for key lookups
      const normalized = original.replace(/\s+/g, ' ').trim();
      const key = normalized.toLowerCase();

      // First try built-in translations map (fast, offline)
      if (targetLang !== 'en' && translations[targetLang] && translations[targetLang][key]) {
        return translations[targetLang][key];
      }

      // If target is French (or any fr-*), try the external API as a fallback
      if (String(targetLang).toLowerCase().startsWith('fr')) {
        try {
          const resp = await fetch(
            `https://api.mymemory.translated.net/get?q=${encodeURIComponent(normalized)}&langpair=en|fr`
          );
          const data = await resp.json();
          if (data && data.responseData && data.responseData.translatedText) return data.responseData.translatedText;
        } catch (err) {
          console.error('Translation API error:', err);
        }
      }

      // No translation available — return the original normalized text
      return normalized;
    }

    async function html_applyTranslation () {
      const svg = document.querySelector('#html_svgPreview svg');
      if (!svg) return;

      const lang = document.getElementById('html_languageSelect').value;
      const texts = svg.querySelectorAll('text');

      for (const txt of texts) {
        // Prefer the stored original text if available; otherwise use the
        // current node text as the source (this handles text nodes that
        // were created after the original mapping was made).
        const storedOrig = html_originalTexts.get(txt);
        const sourceText = (storedOrig && storedOrig.toString().trim()) || (txt.textContent || '').trim();
        if (!sourceText) continue;
        try {
          const translated = await translateText(sourceText, lang);
          txt.textContent = translated;
        } catch (e) {
          console.error('applyTranslation error for text', sourceText, e);
        }
      }

      // reapplyInteractivity();
      // If selected language is not English, show an alternate preview
      // containing the original English texts side-by-side.
      if (lang && lang !== 'en') {
        try { html_createAlternatePreview(); } catch (e) { console.error(e); }
      } else {
        html_removeAlternatePreview();
      }
    }

    // Create an alternate preview SVG showing original English texts.
    function html_createAlternatePreview() {
      // remove existing if any
      html_removeAlternatePreview();
      const liveSvg = document.querySelector('#html_svgPreview svg');
      if (!liveSvg) return;

      // Collect original texts in order from the live DOM
      const liveTexts = Array.from(liveSvg.querySelectorAll('text'));
      const originals = liveTexts.map(t => html_originalTexts.get(t) || (t.textContent || '').trim());

      // Clone the live SVG and replace text nodes with originals
      const altSvg = liveSvg.cloneNode(true);
      const altTexts = Array.from(altSvg.querySelectorAll('text'));
      altTexts.forEach((t, i) => {
        if (originals[i]) t.textContent = originals[i];
      });

      // Wrap in a container for labeling
      const altContainer = document.createElement('div');
      altContainer.id = 'html_svgPreviewAlt';
      altContainer.style.display = 'inline-block';
      altContainer.style.marginLeft = '12px';
      altContainer.style.verticalAlign = 'top';
      altContainer.appendChild(altSvg);

      // Add a small caption
      const cap = document.createElement('div');
      cap.textContent = 'English (Original)';
      cap.style.textAlign = 'center';
      cap.style.fontSize = '12px';
      cap.style.color = '#334155';
      cap.style.marginTop = '6px';
      altContainer.appendChild(cap);

      // Insert after the main preview container
      const previewArea = document.querySelector('.preview-area');
      if (previewArea) previewArea.appendChild(altContainer);
    }

    function html_removeAlternatePreview() {
      const existing = document.getElementById('html_svgPreviewAlt');
      if (existing && existing.parentNode) existing.parentNode.removeChild(existing);
    }

    // ---------- LIVE PREVIEW FOR TEXT INPUTS ----------
    function html_updatePreviewText(translatedOverride) {
      
      const svg = document.querySelector('#html_svgPreview svg');
      if (!svg){
       
         return;
      }else{
       
      }

      // remove any existing transient preview element
      const existing = svg.querySelector('[data-preview="true"]');
      if (existing) existing.remove();
      const rawText = (typeof translatedOverride === 'string' && translatedOverride !== undefined)
        ? translatedOverride
        : (document.getElementById('html_textContent').value || '');
      if (!rawText.trim()) return; // nothing to preview

      const textMode = document.getElementById('html_textMode').value;
      const size = parseFloat(document.getElementById('html_textSize').value) || 16;
      const color = document.getElementById('html_textColorText').value || '#000';
      const fontFamily = document.getElementById('html_fontFamily').value;
      const fontWeight = document.getElementById('html_fontWeight').value;
      const fontStyle = document.getElementById('html_fontStyle').value;
      const letterSpacing = document.getElementById('html_letterSpacing').value;
      const lineHeight = document.getElementById('html_lineHeight').value;
      const x = document.getElementById('html_textX').value;
      const y = document.getElementById('html_textY').value;
      loadGoogleFont(fontFamily);
      if (textMode === 'single') {
        const textEl = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        textEl.setAttribute('data-preview', 'true');
        textEl.setAttribute('x', `${x}%`);
        textEl.setAttribute('y', `${y}%`);
        textEl.setAttribute('text-anchor', 'middle');
        textEl.setAttribute('dominant-baseline', 'middle');
        textEl.setAttribute('font-size', size);
        textEl.setAttribute('font-family', fontFamily);
        textEl.setAttribute('font-weight', fontWeight);
        textEl.setAttribute('font-style', fontStyle);
        textEl.setAttribute('letter-spacing', letterSpacing);
        textEl.setAttribute('fill', color);
        textEl.setAttribute('opacity', '0.85');
        textEl.textContent = rawText;
        svg.appendChild(textEl);
      } else {
        // multiline preview: wrap and position in viewBox coordinates
        const wrapWidth = parseFloat(document.getElementById('html_textWrapWidth').value) || 300;
        const lines = wrapText(rawText, wrapWidth, size, fontFamily);

        // Convert percentage coords to viewBox units
        const viewBox = svg.getAttribute('viewBox').split(/\s+/).map(Number);
        const vbWidth = viewBox[2];
        const vbHeight = viewBox[3];
        const xAbs = (parseFloat(x) / 100) * vbWidth;
        const yAbs = (parseFloat(y) / 100) * vbHeight;

        const textEl = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        textEl.setAttribute('data-preview', 'true');
        textEl.setAttribute('x', xAbs);
        textEl.setAttribute('y', yAbs);
        textEl.setAttribute('text-anchor', 'middle');
        textEl.setAttribute('dominant-baseline', 'middle');
        textEl.setAttribute('font-size', size);
        textEl.setAttribute('font-family', fontFamily);
        textEl.setAttribute('font-weight', fontWeight);
        textEl.setAttribute('font-style', fontStyle);
        textEl.setAttribute('letter-spacing', letterSpacing);
        textEl.setAttribute('fill', color);
        textEl.setAttribute('opacity', '0.85');

        const lineHeight_val = parseFloat(lineHeight) || 1.2;
        const lineSpacing = size * lineHeight_val;

        lines.forEach((line, index) => {
          const tspan = document.createElementNS('http://www.w3.org/2000/svg', 'tspan');
          tspan.setAttribute('x', xAbs);
          if (index === 0) {
            const totalHeight = (lines.length - 1) * lineSpacing;
            const html_offset = -totalHeight / 2;
            if (html_offset !== 0) tspan.setAttribute('dy', html_offset);
          } else {
            tspan.setAttribute('dy', lineSpacing);
          }
          tspan.textContent = line;
          textEl.appendChild(tspan);
        });

        svg.appendChild(textEl);
      }
    }

    // ---------- APPLY CHANGES ----------
    async function html_applyChanges () {
      if (!html_currentSVG) {
        alert('Please upload an SVG file first!');
        return;
      }

      const svg = document.querySelector('#html_svgPreview svg');
      if (!svg) return;

      // CRITICAL: Remove any live preview text before applying permanent changes
      svg.querySelectorAll('[data-preview="true"]').forEach(el => el.remove());
      
      html_applyVisualStyles();

      const lang = document.getElementById('html_languageSelect').value;

      // ---- ADD TEXT ----
      const rawText = document.getElementById('html_textContent').value.trim();
      if (rawText) {
        const translated = await translateText(rawText, lang);
        const size = document.getElementById('html_textSize').value;
        const color = document.getElementById('html_textColorText').value;
        const fontFamily = document.getElementById('html_fontFamily').value;
        const fontWeight = document.getElementById('html_fontWeight').value;
        const fontStyle = document.getElementById('html_fontStyle').value;
        const letterSpacing = document.getElementById('html_letterSpacing').value;
        const lineHeight = document.getElementById('html_lineHeight').value;
        const textMode = document.getElementById('html_textMode').value;
        const x = document.getElementById('html_textX').value;
        const y = document.getElementById('html_textY').value;
        loadGoogleFont(fontFamily);
        if (textMode === 'single') {
          // ---- SINGLE LINE TEXT ----
          const englishText = rawText;
          const textEl = document.createElementNS('http://www.w3.org/2000/svg', 'text');
          textEl.setAttribute('x', `${x}%`);
          textEl.setAttribute('y', `${y}%`);
          textEl.setAttribute('text-anchor', 'middle');
          textEl.setAttribute('font-size', size);
          textEl.setAttribute('font-family', fontFamily);
          textEl.setAttribute('font-weight', fontWeight);
          textEl.setAttribute('font-style', fontStyle);
          textEl.setAttribute('letter-spacing', letterSpacing);
          textEl.setAttribute('line-height', lineHeight);
          textEl.setAttribute('fill', color);
          //textEl.textContent = translated;
          const id = 'text-' + Date.now();
          textEl.setAttribute('data-element-id', id);  
           textEl.textContent = englishText;  // ← English only     
           html_originalTexts.set(textEl, englishText);  // ← Store original 
           //          html_originalTexts.set(textEl, rawText.trim());

          svg.appendChild(textEl);
          html_addedElements.push({ id, type: 'text', content: translated });
        } else {
          // ---- MULTILINE TEXT ----
          const wrapWidth = parseFloat(document.getElementById('html_textWrapWidth').value);
          const lines = wrapText(translated, wrapWidth, size, fontFamily);
          
          // Get viewBox dimensions to convert percentages to absolute coordinates
          const viewBox = svg.getAttribute('viewBox').split(/\s+/).map(Number);
          const vbWidth = viewBox[2];
          const vbHeight = viewBox[3];
          
          // Convert percentage to absolute viewBox coordinates
          const xAbs = (parseFloat(x) / 100) * vbWidth;
          const yAbs = (parseFloat(y) / 100) * vbHeight;
          
          // Create a single text element with tspan for each line
          const textEl = document.createElementNS('http://www.w3.org/2000/svg', 'text');
          const textId = 'text-' + Date.now();
          textEl.setAttribute('data-element-id', textId);
          textEl.setAttribute('x', xAbs);
          textEl.setAttribute('y', yAbs);
          textEl.setAttribute('text-anchor', 'middle');
          textEl.setAttribute('dominant-baseline', 'middle');
          textEl.setAttribute('font-size', size);
          textEl.setAttribute('font-family', fontFamily);
          textEl.setAttribute('font-weight', fontWeight);
          textEl.setAttribute('font-style', fontStyle);
          textEl.setAttribute('letter-spacing', letterSpacing);
          textEl.setAttribute('fill', color);
          
          // Calculate line spacing
          const lineHeight_val = parseFloat(lineHeight);
          const lineSpacing = size * lineHeight_val;
          
          // Add tspan for each line
          lines.forEach((line, index) => {
            const tspan = document.createElementNS('http://www.w3.org/2000/svg', 'tspan');
            tspan.setAttribute('x', xAbs);
            
            // First line gets negative dy to center, rest get positive dy for spacing
            if (index === 0) {
              const totalHeight = (lines.length - 1) * lineSpacing;
              const html_offset = -totalHeight / 2;
              if (html_offset !== 0) {
                tspan.setAttribute('dy', html_offset);
              }
            } else {
              tspan.setAttribute('dy', lineSpacing);
            }
            
            tspan.textContent = line;
            textEl.appendChild(tspan);
          });
          
          svg.appendChild(textEl);
          html_addedElements.push({ id: textId, type: 'multiline-text', content: translated });
          html_originalTexts.set(textEl, rawText.trim());
          
          // Make the text element draggable
          makeDraggable(textEl);
        }
      }

      // ADD ICON (text-based icon or uploaded images handled elsewhere)
      if (html_selectedIcon) {
        const size = document.getElementById('html_iconSize').value;
        const xPx = document.getElementById('html_iconX').value;   // user input (px)
        const yPx = document.getElementById('html_iconY').value;   // user input (px)

        // ---- NEW: convert px → viewBox coordinates -----------------
        const svgEl = document.querySelector('#html_svgPreview svg');
        const viewBox = svgEl.getAttribute('viewBox').split(/\s+/).map(Number);
        const vbWidth = viewBox[2];
        const vbHeight = viewBox[3];

        // Use the SVG's rendered size so border/padding on the parent
        // container won't change the px->viewBox conversion.
        const svgRect = svgEl.getBoundingClientRect();
        const containerW = svgRect.width || svgEl.parentElement.clientWidth || vbWidth;
        const containerH = svgRect.height || svgEl.parentElement.clientHeight || vbHeight;

        const scaleX = vbWidth  / (containerW || vbWidth);
        const scaleY = vbHeight / (containerH || vbHeight);

        const x = +xPx * scaleX;   // viewBox units
        const y = +yPx * scaleY;   // viewBox units
        // -----------------------------------------------------------

        const iconEl = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        iconEl.setAttribute('x', x);
        iconEl.setAttribute('y', y);
        iconEl.setAttribute('font-size', size);
        iconEl.setAttribute('text-anchor', 'middle');
        iconEl.setAttribute('dominant-baseline', 'central');
        iconEl.textContent = html_selectedIcon;

        const id = 'icon-' + Date.now();
        iconEl.setAttribute('data-element-id', id);   // <-- needed for drag & erase

        svgEl.appendChild(iconEl);
        html_addedElements.push({ id, type: 'icon', content: html_selectedIcon });

        // make it draggable immediately
        makeDraggable(iconEl);
    }

    //  if (lang === 'fr-CA') {     await updateFrenchPreviewOnly();   }

      html_reapplyInteractivity();
      html_updateElementList();
      await html_applyTranslation ();
    }



    function makeSelectable(el) {
        el.addEventListener("mousedown", e => {
            if (html_currentMode === "edit") {
                selectElement(el);
                e.stopPropagation();
            }
        });
    }

function selectElement(el) {
    // remove old highlight
    if (html_selectionBox) {
        html_selectionBox.remove();
        html_selectionBox = null;
    }

    // clear old selected class
    if (html_selectedElement) {
        html_selectedElement.classList.remove("selected-element");
    }

    html_selectedElement = el;
    html_selectedElement.classList.add("selected-element");

    // ---- CREATE SELECTION HIGHLIGHT RECT ----
    const parent = el.parentNode; // <-- insert inside parent, not always svg
    const bbox = el.getBBox();

    const bgColor = document.getElementById('html_bgColorText').value;
    const borderColor = document.getElementById('html_borderColorText').value;
    const borderWidth = parseFloat(document.getElementById('html_borderWidth').value);
    const borderRadius = parseFloat(document.getElementById('html_borderRadius').value);

    html_selectionBox = document.createElementNS("http://www.w3.org/2000/svg", "rect");
    html_selectionBox.setAttribute("x", bbox.x - borderWidth);
    html_selectionBox.setAttribute("y", bbox.y - borderWidth);
    html_selectionBox.setAttribute("width", bbox.width + borderWidth * 2);
    html_selectionBox.setAttribute("height", bbox.height + borderWidth * 2);
    html_selectionBox.setAttribute("fill", bgColor);
    html_selectionBox.setAttribute("stroke", borderColor);
    html_selectionBox.setAttribute("stroke-width", borderWidth);
    // Keep selection helper rectangular so it doesn't inherit canvas rounding
    html_selectionBox.setAttribute("rx", 0);
    html_selectionBox.setAttribute("pointer-events", "none");



    // insert directly before element to sit behind it
    parent.insertBefore(html_selectionBox, el);
}


    // ---------- ERASE MODE ----------
    function html_makeElementsErasable() {
      const svg = document.querySelector('#html_svgPreview svg');
      if (!svg) return;
      svg.querySelectorAll('text, circle, rect, path, polygon, line, ellipse, polyline, image').forEach(el => {
        if (!el.hasAttribute('data-background')) {
          el.classList.add('erasable-element');
          el.onclick = e => {
            if (html_currentMode === 'erase') {
              e.stopPropagation();
              eraseElement(el);
            }
          };
        }
      });
    }

    function html_removeErasableClasses() {
      document.querySelectorAll('.erasable-element').forEach(el => {
        el.classList.remove('erasable-element');
        el.onclick = null;
      });
    }

    function eraseElement(el) {
      if (confirm('Delete this element?')) {
        const id = el.getAttribute('data-element-id');
        if (id) {
          html_addedElements = html_addedElements.filter(item => item.id !== id);
        }
        el.remove();
        html_updateElementList();
      }
    }

    // ---------- ELEMENT LIST ----------
    function html_updateElementList() {
      const container = document.getElementById('html_elementList');
      if (html_addedElements.length === 0) {
        container.innerHTML = '<p style="color: #999; text-align: center; font-size: 12px;">No elements added yet</p>';
        return;
      }
      container.innerHTML = '';
      html_addedElements.forEach(item => {
        const div = document.createElement('div');
        div.className = 'element-item';
        const contentShort = (item.content || '').toString().substring(0,15);
        div.innerHTML = `<span>${item.type}: ${contentShort}${(item.content||'').toString().length > 15 ? '...' : ''}</span>
                         <button class="delete-btn" onclick="deleteAddedElement('${item.id}')">Delete</button>`;
        container.appendChild(div);
      });
    }

    window.deleteAddedElement = function (id) {
      const el = document.querySelector(`[data-element-id="${id}"]`);
      if (el) el.remove();
      html_addedElements = html_addedElements.filter(item => item.id !== id);
      html_updateElementList();
    };

    function clearAllElements() {
      if (html_addedElements.length === 0) {
        alert('No elements to clear!');
        return;
      }
      if (confirm('Clear all added elements?')) {
        html_addedElements.forEach(item => {
          const el = document.querySelector(`[data-element-id="${item.id}"]`);
          if (el) el.remove();
        });
        html_addedElements = [];
        html_updateElementList();
      }
    }

    // ---------- DRAG FUNCTIONALITY ----------
    function makeDraggable(el) {
      if (el.hasAttribute('data-background')) return;

      el.style.cursor = 'move';
      el.addEventListener('mousedown', function (e) {
        if (html_currentMode !== 'edit') return;
        e.preventDefault();
        html_dragged = el;

        const svg = el.ownerSVGElement;
        const pt = svg.createSVGPoint();
        const rect = el.getBBox();

        pt.x = e.clientX;
        pt.y = e.clientY;
        const cursorPt = pt.matrixTransform(svg.getScreenCTM().inverse());

        html_offset.x = cursorPt.x - rect.x - (rect.width / 2);
        html_offset.y = cursorPt.y - rect.y - (rect.height / 2);
      });
    }

    document.addEventListener('mousemove', function (e) {
      if (!html_dragged) return;

      const svg = html_dragged.ownerSVGElement;
      const pt = svg.createSVGPoint();
      pt.x = e.clientX;
      pt.y = e.clientY;
      const cursorPt = pt.matrixTransform(svg.getScreenCTM().inverse());

      const newX = cursorPt.x - html_offset.x;
      const newY = cursorPt.y - html_offset.y;

      if (html_dragged.tagName === 'text') {
        // Check if it has tspans (multiline text) or is single line
        const hasMultiline = html_dragged.querySelector('tspan');
        
        if (hasMultiline) {
          // For multiline text, update x and y attributes
          html_dragged.setAttribute('x', newX);
          html_dragged.setAttribute('y', newY);
          
          // Update all tspans to have the same x coordinate
          html_dragged.querySelectorAll('tspan').forEach(tspan => {
            tspan.setAttribute('x', newX);
          });
        } else {
          // For single line text, just update x and y
          html_dragged.setAttribute('x', newX);
          html_dragged.setAttribute('y', newY);
        }
      } else if (html_dragged.tagName === 'g') {
        // For groups, update transform
        html_dragged.setAttribute('transform', `translate(${newX}, ${newY})`);
      } else if (html_dragged.tagName === 'image') {
        // For images set x/y directly
        html_dragged.setAttribute('x', newX);
        html_dragged.setAttribute('y', newY);
      }
    });

    document.addEventListener('mouseup', function () {
      html_dragged = null;
    });

    function html_reapplyInteractivity() {
      const svg = document.querySelector('#html_svgPreview svg');
      if (!svg) return;

      // --- REMOVE ALL ERASE HANDLERS IN EDIT MODE ---
      if (html_currentMode === "edit") {
          svg.querySelectorAll('.erasable-element').forEach(el => {
              el.classList.remove("erasable-element");
              el.onclick = null;
          });
      }

      // --- MAKE ALL ELEMENTS SELECTABLE IN EDIT MODE ---
      svg.querySelectorAll('text, circle, rect, path, polygon, line, ellipse, polyline, image')
        .forEach(el => {
            if (!el.hasAttribute('data-background')) {
                makeSelectable(el);
            }
      });

      // --- DRAGGABLE ONLY FOR OUR CUSTOM ADDED ELEMENTS ---
      svg.querySelectorAll('[data-element-id]').forEach(el => {
        makeDraggable(el);
      });


      if (html_currentMode === 'erase') {
        html_makeElementsErasable();
      }
    }

    function html_updateSVGViewBox() {
        const svg = document.querySelector('#html_svgPreview svg');
        if (!svg) return;

        const newW = parseFloat(document.getElementById('html_widthInput').value);
        const newH = parseFloat(document.getElementById('html_heightInput').value);

        // Update BOTH viewBox and physical size
        svg.setAttribute('viewBox', `0 0 ${newW} ${newH}`);
        svg.setAttribute('width', String(newW));
        svg.setAttribute('height', String(newH));
        svg.style.width = `${newW}px`;
        svg.style.height = `${newH}px`;
    }


    function resizeInternalShapes() {
        const svg = document.querySelector('#html_svgPreview svg');
        if (!svg) return;

        const newW = parseFloat(document.getElementById('html_widthInput').value);
        const newH = parseFloat(document.getElementById('html_heightInput').value);

        // Change the main rect with fixed size (your example)
        const rect = svg.querySelector('#Rectangle_1756 rect');
        if (rect) {
            rect.setAttribute('width', newW);
            rect.setAttribute('height', newH);
        }
    }

    // ---------- DOWNLOAD SVG----------
    function html_downloadSVG() {
      const svg = document.querySelector('#html_svgPreview svg');
      const container = document.getElementById('html_svgPreview');
      if (!svg || !container) return alert('No SVG to download!');
      // Remove any transient selection highlight from the live preview before cloning
      if (typeof html_selectionBox !== 'undefined' && html_selectionBox) {
        html_selectionBox.remove();
        html_selectionBox = null;
      }
      // Get current settings
      const inputInnerW = parseFloat(document.getElementById('html_widthInput').value) || 249;
      const inputInnerH = parseFloat(document.getElementById('html_heightInput').value) || 64;
      const borderW = parseFloat(document.getElementById('html_borderWidth').value) || 0;
      const borderR = parseFloat(document.getElementById('html_borderRadius').value) || 0;
      const borderColor = document.getElementById('html_borderColorText').value || '#000';
      const bgColor = document.getElementById('html_bgColorText').value || '#fff';
 
      const exportW = Math.round(inputInnerW + borderW * 2);
      const exportH = Math.round(inputInnerH + borderW * 2);
 
      const xmlns = 'http://www.w3.org/2000/svg';
 
      // Function to generate export SVG from a given source SVG element
      function generateExportSVG(sourceSvg, filename) {
        const out = document.createElementNS(xmlns, 'svg');
        out.setAttribute('width', String(exportW));
        out.setAttribute('height', String(exportH));
        out.setAttribute('viewBox', `0 0 ${exportW} ${exportH}`);
        out.setAttribute('xmlns', xmlns);
 
        // Create a clipPath for perfect rounded corners
        const clipId = 'rounded-clip';
        const clipPath = document.createElementNS(xmlns, 'clipPath');
        clipPath.setAttribute('id', clipId);
 
        const clipRect = document.createElementNS(xmlns, 'rect');
        clipRect.setAttribute('x', String(borderW));
        clipRect.setAttribute('y', String(borderW));
        clipRect.setAttribute('width', String(inputInnerW));
        clipRect.setAttribute('height', String(inputInnerH));
        clipRect.setAttribute('rx', String(borderR));
        clipRect.setAttribute('ry', String(borderR));
        clipPath.appendChild(clipRect);
        out.appendChild(clipPath);
 
        // Group for all inner content (background + artwork)
        const contentGroup = document.createElementNS(xmlns, 'g');
        contentGroup.setAttribute('clip-path', `url(#${clipId})`);
 
        // Background rect (fills the clipped area)
        const bgRect = document.createElementNS(xmlns, 'rect');
        bgRect.setAttribute('x', String(borderW));
        bgRect.setAttribute('y', String(borderW));
        bgRect.setAttribute('width', String(inputInnerW));
        bgRect.setAttribute('height', String(inputInnerH));
        bgRect.setAttribute('fill', bgColor);
        contentGroup.appendChild(bgRect);
 
        // Clone and clean inner SVG content
        const innerClone = sourceSvg.cloneNode(true);
        innerClone.querySelectorAll('.erasable-element, .selected-element').forEach(el => {
          el.classList.remove('erasable-element', 'selected-element');
        });
        innerClone.querySelectorAll('[data-preview="true"], [pointer-events="none"], .selection-box').forEach(el => el.remove());
        innerClone.querySelectorAll('[style*="cursor"], [onclick]').forEach(el => {
          el.removeAttribute('style');
          el.removeAttribute('onclick');
        });
        innerClone.querySelectorAll('[data-element-id]').forEach(el => el.removeAttribute('style'));
 
        // Append all children into a group and shift by border width
        const innerGroup = document.createElementNS(xmlns, 'g');
        innerGroup.setAttribute('transform', `translate(${borderW}, ${borderW})`);
        Array.from(innerClone.childNodes).forEach(node => {
          if (node.nodeType === 1) { // Element nodes only
            innerGroup.appendChild(node.cloneNode(true));
          }
        });
        contentGroup.appendChild(innerGroup);
        out.appendChild(contentGroup);
 
        // Optional: Add visible border (on top)
        if (borderW > 0) {
          const borderRect = document.createElementNS(xmlns, 'rect');
          borderRect.setAttribute('x', String(borderW / 2));
          borderRect.setAttribute('y', String(borderW / 2));
          borderRect.setAttribute('width', String(exportW - borderW));
          borderRect.setAttribute('height', String(exportH - borderW));
          borderRect.setAttribute('rx', String(borderR));
          borderRect.setAttribute('ry', String(borderR));
          borderRect.setAttribute('fill', 'none');
          borderRect.setAttribute('stroke', borderColor);
          borderRect.setAttribute('stroke-width', String(borderW));
          out.appendChild(borderRect); // drawn on top
        }
 
        // Serialize
        const serializer = new XMLSerializer();
        let source = serializer.serializeToString(out);
        source = '<?xml version="1.0" standalone="no"?>\n' + source;
 
        // Download
        const blob = new Blob([source], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      }
 
      // Always download English
      generateExportSVG(svg, 'edited-svg-en.svg');
 
      // If French is selected and visible, download French as well
      const frenchBox = document.getElementById('html_frenchCtaBox');
      if (frenchBox && frenchBox.style.display !== 'none') {
        const frenchSvg = document.querySelector('#svgFrenchPreview svg');
        if (frenchSvg) {
          generateExportSVG(frenchSvg, 'edited-svg-fr.svg');
        }
      }
    }

    // Add this helper to produce a standalone SVG string suitable for saving/downloading.
function exportSVGSource() {
    const svg = document.querySelector('#html_svgPreview svg');
    if (!svg) return null;

    // Gather settings (fall back to SVG's attributes)
    const inputInnerW = parseFloat(document.getElementById('html_widthInput').value) || parseFloat(svg.getAttribute('width')) || 249;
    const inputInnerH = parseFloat(document.getElementById('html_heightInput').value) || parseFloat(svg.getAttribute('height')) || 64;
    const borderW = parseFloat(document.getElementById('html_borderWidth').value) || 0;
    const borderR = parseFloat(document.getElementById('html_borderRadius').value) || 0;
    const borderColor = document.getElementById('html_borderColorText').value || '#000';
    const bgColor = document.getElementById('html_bgColorText').value || '#fff';

    const exportW = Math.round(inputInnerW + borderW * 2);
    const exportH = Math.round(inputInnerH + borderW * 2);

    const xmlns = 'http://www.w3.org/2000/svg';
    const xlink = 'http://www.w3.org/1999/xlink';

    // Create container SVG
    const out = document.createElementNS(xmlns, 'svg');
    out.setAttribute('xmlns', xmlns);
    out.setAttribute('xmlns:xlink', xlink);
    out.setAttribute('width', String(exportW));
    out.setAttribute('height', String(exportH));
    out.setAttribute('viewBox', `0 0 ${exportW} ${exportH}`);

    // defs + clipPath for rounded corners
    const defs = document.createElementNS(xmlns, 'defs');
    const clipId = 'export-clip-' + Date.now();
    const clipPath = document.createElementNS(xmlns, 'clipPath');
    clipPath.setAttribute('id', clipId);
    const clipRect = document.createElementNS(xmlns, 'rect');
    clipRect.setAttribute('x', String(borderW));
    clipRect.setAttribute('y', String(borderW));
    clipRect.setAttribute('width', String(inputInnerW));
    clipRect.setAttribute('height', String(inputInnerH));
    clipRect.setAttribute('rx', String(borderR));
    clipRect.setAttribute('ry', String(borderR));
    clipPath.appendChild(clipRect);
    defs.appendChild(clipPath);
    out.appendChild(defs);

    // group content with clipping
    const contentGroup = document.createElementNS(xmlns, 'g');
    contentGroup.setAttribute('clip-path', `url(#${clipId})`);

    // background rect (fills clipped area)
    const bgRect = document.createElementNS(xmlns, 'rect');
    bgRect.setAttribute('x', String(borderW));
    bgRect.setAttribute('y', String(borderW));
    bgRect.setAttribute('width', String(inputInnerW));
    bgRect.setAttribute('height', String(inputInnerH));
    bgRect.setAttribute('fill', bgColor);
    contentGroup.appendChild(bgRect);

    // clone live SVG and clean editor artifacts
    const innerClone = svg.cloneNode(true);

    // remove transient/editor-only nodes/attributes
    innerClone.querySelectorAll('[data-preview="true"], .erasable-element, .selected-element, .selection-box').forEach(el => el.remove());
    innerClone.querySelectorAll('[onclick]').forEach(el => el.removeAttribute('onclick'));
    innerClone.querySelectorAll('[data-js-selected]').forEach(el => el.removeAttribute('data-js-selected'));
    // clean style strings (remove cursor/pointer-events added for editor)
    innerClone.querySelectorAll('[style]').forEach(el => {
        const s = el.getAttribute('style') || '';
        const cleaned = s.replace(/cursor\s*:\s*[^;]+;?/gi, '').replace(/pointer-events\s*:\s*[^;]+;?/gi, '');
        if (cleaned.trim()) el.setAttribute('style', cleaned); else el.removeAttribute('style');
    });

    // Move children into an inner group html_offset by border width
    const innerGroup = document.createElementNS(xmlns, 'g');
    innerGroup.setAttribute('transform', `translate(${borderW}, ${borderW})`);
    Array.from(innerClone.childNodes).forEach(node => {
        if (node.nodeType === 1) innerGroup.appendChild(node.cloneNode(true));
    });
    contentGroup.appendChild(innerGroup);
    out.appendChild(contentGroup);

    // draw visible border on top if requested
    if (borderW > 0) {
        const borderRect = document.createElementNS(xmlns, 'rect');
        borderRect.setAttribute('x', String(borderW / 2));
        borderRect.setAttribute('y', String(borderW / 2));
        borderRect.setAttribute('width', String(exportW - borderW));
        borderRect.setAttribute('height', String(exportH - borderW));
        borderRect.setAttribute('rx', String(borderR));
        borderRect.setAttribute('ry', String(borderR));
        borderRect.setAttribute('fill', 'none');
        borderRect.setAttribute('stroke', borderColor);
        borderRect.setAttribute('stroke-width', String(borderW));
        out.appendChild(borderRect);
    }

    try {
        const serializer = new XMLSerializer();
        let source = serializer.serializeToString(out);
        source = '<?xml version="1.0" standalone="no"?>\n' + source;
        return source;
    } catch (err) {
        console.error('exportSVGSource serialization error', err);
        return null;
    }
}

    // clicking outside clears selection
    document.addEventListener("mousedown", e => {
      const svgPreview = document.getElementById("html_svgPreview");
        // if (!document.getElementById("html_svgPreview").contains(e.target)) {
        if (svgPreview && !svgPreview.contains(e.target)) {
            if (html_selectedElement) {
                html_selectedElement.classList.remove("selected-element");
                html_selectedElement = null;
            }
            if (html_selectionBox) {
                html_selectionBox.remove();
                html_selectionBox = null;
            }
        }
    });

    // ---------- INITIALISE ----------
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelector('.mode-btn:not(.erase-mode)').classList.add('active');
      html_currentMode = 'edit';
 
      // Live updates
      ['html_widthInput', 'html_heightInput', 'html_borderWidth', 'html_borderRadius', 'html_borderColorText', 'html_bgColorText','html_iconSize'].forEach(id => {
        const el = document.getElementById(id);
         if (el) el.addEventListener('input', () => {
            html_applyVisualStyles();
            html_updateSVGViewBox();
            html_resizeInternalShapes();
            try { html_updateUploadedImagesSize(); } catch (err) {}
        });
      });
 
      // Text-related live preview wiring
      ['html_textContent','html_textX','html_textY','html_textSize','html_fontFamily','html_fontWeight','html_fontStyle','html_letterSpacing','html_lineHeight','html_textMode','html_textWrapWidth','html_textColorText','html_textColorPicker'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        const ev = el.tagName.toLowerCase() === 'select' || el.type === 'file' ? 'change' : 'input';
        el.addEventListener(ev, () => {
          try { html_updatePreviewText(); } catch (e) {}
        });
      });
 
      // initial preview run
      try { html_updatePreviewText(); } catch (e) {}
    });


// HTML UTILITY FUNCTIONS





</script>