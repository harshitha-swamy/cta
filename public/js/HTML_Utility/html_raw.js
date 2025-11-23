// HTML UTILITY FUNCTIONS
    let html_currentSVG = null;
    let html_selectedIcon = '';
    let html_currentMode = 'edit';
    let html_addedElements = [];
    let html_originalTexts = new WeakMap();
    let html_dragged = null;
    let offset = { x: 0, y: 0 };
    let html_currentViewBox = { w: 500, h: 500 };
    let html_selectedElement = null;
    let html_selectionBox = null;


    const html_translations = {
      'en': {},
      'fr-CA': {
        'hello': 'Bonjour',
        'welcome': 'Bienvenue',
        'thank you': 'Merci',
        'yes': 'Oui',
        'no': 'Non',
        'upload an svg to begin': 'Téléchargez un SVG pour commencer',
        'edit': 'Éditer',
        'erase': 'Effacer'
      }
    };

    // function html_loadDefaultSVG() {
    //   alert(11);
    //   const sample = `
    // <svg xmlns="http://www.w3.org/2000/svg" width="249" height="64" viewBox="0 0 249 64">
    //   <rect width="249" height="64" fill="#ffffff" data-background="true"/>
    //   <text x="50%" y="50%" text-anchor="middle" dominant-baseline="middle" font-size="14" fill="#0f172a">Sample</text>
    // </svg>`;
    //   html_currentSVG = sample;
    //   alert(22);();
    //   html_applyTranslation();
    // }

   window.html_loadDefaultSVG = function() {
    alert(11);
  const sample = `
    <svg xmlns="http://www.w3.org/2000/svg" width="249" height="64" viewBox="0 0 249 64">
      <rect width="249" height="64" fill="#ffffff" data-background="true"/>
      <text x="50%" y="50%" text-anchor="middle" dominant-baseline="middle" font-size="14" fill="#0f172a">Sample</text>
    </svg>`;
  alert(22);
  html_currentSVG = sample;
  html_displaySVG();
  html_applyTranslation();
};





    // ---------- FILE UPLOAD (SVG) ----------
    document.getElementById('html_svgUpload').addEventListener('change', function (e) {
      const file = e.target.files[0];
      if (file && file.type === 'image/svg+xml') {
        const reader = new FileReader();
        reader.onload = async function (event) {
          html_currentSVG = event.target.result;
          displaySVG();
          await applyTranslation();
          document.getElementById('referenceCtaSvgContainer').innerHTML = event.target.result;
        };
        reader.readAsText(file);
      }
    });

    // ---------- COLOR SYNC ----------
    document.getElementById('borderColorPicker').addEventListener('input', e => {
      document.getElementById('borderColorText').value = e.target.value;
      // update live visuals when user uses the color picker
      try { applyVisualStyles(); } catch (err) {}
    });
    document.getElementById('borderColorText').addEventListener('input', e => {
      document.getElementById('borderColorPicker').value = e.target.value;
      try { applyVisualStyles(); } catch (err) {}
    });
    document.getElementById('textColorPicker').addEventListener('input', e => {
      document.getElementById('textColorText').value = e.target.value;
      try { updatePreviewText(); } catch (err) {}
    });
    document.getElementById('textColorText').addEventListener('input', e => {
      document.getElementById('textColorPicker').value = e.target.value;
      try { updatePreviewText(); } catch (err) {}
    });
    document.getElementById('bgColorPicker').addEventListener('input', e => {
      document.getElementById('bgColorText').value = e.target.value;
      try { applyVisualStyles(); } catch (err) {}
    });
    document.getElementById('bgColorText').addEventListener('input', e => {
      document.getElementById('bgColorPicker').value = e.target.value;
      try { applyVisualStyles(); } catch (err) {}
    });

    // ---------- ICON IMAGE UPLOAD (multiple images, default placement, draggable) ----------
    // We'll accept multiple files from #iconUpload and insert each as an <image> into the current svg.
    const uploadInput = document.getElementById("iconUpload");
    const iconX = document.getElementById("iconX");
    const iconY = document.getElementById("iconY");
    const iconSize = document.getElementById("iconSize");

    uploadInput.addEventListener("change", function () {
        const files = Array.from(this.files || []);
        if (!files.length) return;

        // Insert each file
        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                const base64 = e.target.result;
                insertUploadedImage(base64, file.name);
            };
            reader.readAsDataURL(file);
        });

        // clear the input so same file can be picked again if needed
        this.value = '';
    });

    // Insert image into current SVG with default px placement, convert to viewBox units
    function insertUploadedImage(base64url, filename) {
        const svg = document.querySelector('#svgPreview svg');
        if (!svg) {
            console.error('No SVG available to insert image into.');
            return;
        }

        // Ensure xlink namespace exists
        if (!svg.getAttribute('xmlns:xlink')) svg.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');

        // default pixel placement (Option A)
        const defaultXpx = parseFloat(iconX.value) || 50;   // we still respect the default inputs if user changed them
        const defaultYpx = parseFloat(iconY.value) || 50;
        const defaultWpx = parseFloat(iconSize.value) || 120;

        // Convert px to viewBox units
        const viewBox = svg.getAttribute('viewBox').split(/\s+/).map(Number);
        const vbWidth = viewBox[2];
        const vbHeight = viewBox[3];

        // container (visible) pixel size
        // Use the SVG's actual rendered pixel size to compute scale so
        // editor chrome (borders) applied to the container don't change
        // the conversion from px -> viewBox units.
        const svgRect = svg.getBoundingClientRect();
        const containerW = svgRect.width || svg.parentElement.clientWidth || vbWidth;
        const containerH = svgRect.height || svg.parentElement.clientHeight || vbHeight;

        // scale to convert px -> viewBox units (use width-based scale)
        const scale = vbWidth / (containerW || vbWidth);

        const x_vb = defaultXpx * scale;
        const y_vb = defaultYpx * scale;
        const w_vb = defaultWpx * scale;

        // Create SVG <image>
        const imgEl = document.createElementNS('http://www.w3.org/2000/svg', 'image');
        imgEl.setAttributeNS('http://www.w3.org/1999/xlink', 'href', base64url);

        // To preserve aspect ratio, we need natural image dimensions — create an Image object
        const tmpImg = new Image();
        tmpImg.onload = function() {
            const natW = tmpImg.naturalWidth || 1;
            const natH = tmpImg.naturalHeight || 1;
            const aspect = natH / natW;
            const h_vb = w_vb * aspect;

            imgEl.setAttribute('x', String(x_vb));
            imgEl.setAttribute('y', String(y_vb));
            imgEl.setAttribute('width', String(w_vb));
            imgEl.setAttribute('height', String(h_vb));
            imgEl.setAttribute('preserveAspectRatio', 'xMidYMid meet');

            const id = 'img-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
            imgEl.setAttribute('data-element-id', id);

          // store natural size and base px width so we can update size later
          imgEl.setAttribute('data-natw', String(natW));
          imgEl.setAttribute('data-nath', String(natH));
          imgEl.setAttribute('data-base-width-px', String(defaultWpx));

            svg.appendChild(imgEl);

            // Record in addedElements list
            addedElements.push({ id, type: 'image', content: filename || 'uploaded-image' });

            // Make interactive
            reapplyInteractivity();
            updateElementList();
        };
        tmpImg.src = base64url;
    }

    // ---------- LANGUAGE CHANGE ----------
    // When language changes, translate existing SVG texts AND update
    // the live textarea preview (without overwriting user input).
    document.getElementById('languageSelect').addEventListener('change', async () => {
      await applyTranslation();
      try {
        const lang = document.getElementById('languageSelect').value;
        const raw = (document.getElementById('textContent').value || '').trim();
        if (raw) {
          const translated = await translateText(raw, lang);
          updatePreviewText(translated);
        }
      } catch (err) { console.error(err); }
    });

    // ---------- TEXT MODE TOGGLE ----------
    document.getElementById('textMode').addEventListener('change', function(e) {
      const multilineOptions = document.getElementById('multilineOptions');
      if (e.target.value === 'multi') {
        multilineOptions.style.display = 'block';
      } else {
        multilineOptions.style.display = 'none';
      }
    });

    // ---------- MODE SWITCH ----------
    function setMode(mode, btn) {
      currentMode = mode;
      document.querySelectorAll('.mode-btn').forEach(btn => btn.classList.remove('active'));
      btn.classList.add('active');
      const svg = document.querySelector('#svgPreview svg');
      if (svg) {
        if (mode === 'erase') {
          svg.classList.add('erase-cursor');
          makeElementsErasable();
        } else {
          svg.classList.remove('erase-cursor');
          removeErasableClasses();
        }
      }
      reapplyInteractivity();
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
          applyTo('#svgPreview text, #svgPreview tspan');
          applyTo('#htmlPreview text, #htmlPreview tspan');
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
    function displaySVG() {
      if (!currentSVG) return;

      const parser = new DOMParser();
      const svgDoc = parser.parseFromString(currentSVG, 'image/svg+xml');
      const svgElement = svgDoc.documentElement;

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
        bgRect.setAttribute('fill', document.getElementById('bgColorText').value);
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
        if (!originalTexts.has(text)) {
          originalTexts.set(text, text.textContent.trim());
        }
      });

      applyVisualStyles();
      reapplyInteractivity();
      addedElements = [];
      updateElementList();
      // update live preview text if user is typing
      try { updatePreviewText(); } catch (e) { /* ignore if preview not ready */ }
    }

    // ---------- UPDATE UPLOADED IMAGES SIZE ----------
    function updateUploadedImagesSize() {
      const svg = document.querySelector('#svgPreview svg');
      if (!svg) return;

      const images = Array.from(svg.querySelectorAll('image[data-natw]'));
      if (!images.length) return;

      const iconSizePx = parseFloat(document.getElementById('iconSize').value) || 120;

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
    function applyVisualStyles() {
      const svg = document.querySelector('#svgPreview svg');
      if (!svg) return;

      const width = parseFloat(document.getElementById('widthInput').value);
      const height = parseFloat(document.getElementById('heightInput').value);
      const borderColor = document.getElementById('borderColorText').value;
      const borderWidth = parseFloat(document.getElementById('borderWidth').value);
      const borderRadius = parseFloat(document.getElementById('borderRadius').value);
      const bgColor = document.getElementById('bgColorText').value;

    const container = document.getElementById('svgPreview');
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

    async function applyTranslation() {
      const svg = document.querySelector('#svgPreview svg');
      if (!svg) return;

      const lang = document.getElementById('languageSelect').value;
      const texts = svg.querySelectorAll('text');

      for (const txt of texts) {
        // Prefer the stored original text if available; otherwise use the
        // current node text as the source (this handles text nodes that
        // were created after the original mapping was made).
        const storedOrig = originalTexts.get(txt);
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
        try { createAlternatePreview(); } catch (e) { console.error(e); }
      } else {
        removeAlternatePreview();
      }
    }

    // Create an alternate preview SVG showing original English texts.
    function createAlternatePreview() {
      // remove existing if any
      removeAlternatePreview();
      const liveSvg = document.querySelector('#svgPreview svg');
      if (!liveSvg) return;

      // Collect original texts in order from the live DOM
      const liveTexts = Array.from(liveSvg.querySelectorAll('text'));
      const originals = liveTexts.map(t => originalTexts.get(t) || (t.textContent || '').trim());

      // Clone the live SVG and replace text nodes with originals
      const altSvg = liveSvg.cloneNode(true);
      const altTexts = Array.from(altSvg.querySelectorAll('text'));
      altTexts.forEach((t, i) => {
        if (originals[i]) t.textContent = originals[i];
      });

      // Wrap in a container for labeling
      const altContainer = document.createElement('div');
      altContainer.id = 'svgPreviewAlt';
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

    function removeAlternatePreview() {
      const existing = document.getElementById('svgPreviewAlt');
      if (existing && existing.parentNode) existing.parentNode.removeChild(existing);
    }

    // ---------- LIVE PREVIEW FOR TEXT INPUTS ----------
    function updatePreviewText(translatedOverride) {
      
      const svg = document.querySelector('#svgPreview svg');
      if (!svg){
       
         return;
      }else{
       
      }

      // remove any existing transient preview element
      const existing = svg.querySelector('[data-preview="true"]');
      if (existing) existing.remove();
      const rawText = (typeof translatedOverride === 'string' && translatedOverride !== undefined)
        ? translatedOverride
        : (document.getElementById('textContent').value || '');
      if (!rawText.trim()) return; // nothing to preview

      const textMode = document.getElementById('textMode').value;
      const size = parseFloat(document.getElementById('textSize').value) || 16;
      const color = document.getElementById('textColorText').value || '#000';
      const fontFamily = document.getElementById('fontFamily').value;
      const fontWeight = document.getElementById('fontWeight').value;
      const fontStyle = document.getElementById('fontStyle').value;
      const letterSpacing = document.getElementById('letterSpacing').value;
      const lineHeight = document.getElementById('lineHeight').value;
      const x = document.getElementById('textX').value;
      const y = document.getElementById('textY').value;
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
        const wrapWidth = parseFloat(document.getElementById('textWrapWidth').value) || 300;
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
            const offset = -totalHeight / 2;
            if (offset !== 0) tspan.setAttribute('dy', offset);
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
    async function applyChanges() {
      if (!currentSVG) {
        alert('Please upload an SVG file first!');
        return;
      }

      const svg = document.querySelector('#svgPreview svg');
      if (!svg) return;

      // CRITICAL: Remove any live preview text before applying permanent changes
      svg.querySelectorAll('[data-preview="true"]').forEach(el => el.remove());
      
      applyVisualStyles();

      const lang = document.getElementById('languageSelect').value;

      // ---- ADD TEXT ----
      const rawText = document.getElementById('textContent').value.trim();
      if (rawText) {
        const translated = await translateText(rawText, lang);
        const size = document.getElementById('textSize').value;
        const color = document.getElementById('textColorText').value;
        const fontFamily = document.getElementById('fontFamily').value;
        const fontWeight = document.getElementById('fontWeight').value;
        const fontStyle = document.getElementById('fontStyle').value;
        const letterSpacing = document.getElementById('letterSpacing').value;
        const lineHeight = document.getElementById('lineHeight').value;
        const textMode = document.getElementById('textMode').value;
        const x = document.getElementById('textX').value;
        const y = document.getElementById('textY').value;
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
           originalTexts.set(textEl, englishText);  // ← Store original 
           //          originalTexts.set(textEl, rawText.trim());

          svg.appendChild(textEl);
          addedElements.push({ id, type: 'text', content: translated });
        } else {
          // ---- MULTILINE TEXT ----
          const wrapWidth = parseFloat(document.getElementById('textWrapWidth').value);
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
              const offset = -totalHeight / 2;
              if (offset !== 0) {
                tspan.setAttribute('dy', offset);
              }
            } else {
              tspan.setAttribute('dy', lineSpacing);
            }
            
            tspan.textContent = line;
            textEl.appendChild(tspan);
          });
          
          svg.appendChild(textEl);
          addedElements.push({ id: textId, type: 'multiline-text', content: translated });
          originalTexts.set(textEl, rawText.trim());
          
          // Make the text element draggable
          makeDraggable(textEl);
        }
      }

      // ADD ICON (text-based icon or uploaded images handled elsewhere)
      if (selectedIcon) {
        const size = document.getElementById('iconSize').value;
        const xPx = document.getElementById('iconX').value;   // user input (px)
        const yPx = document.getElementById('iconY').value;   // user input (px)

        // ---- NEW: convert px → viewBox coordinates -----------------
        const svgEl = document.querySelector('#svgPreview svg');
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
        iconEl.textContent = selectedIcon;

        const id = 'icon-' + Date.now();
        iconEl.setAttribute('data-element-id', id);   // <-- needed for drag & erase

        svgEl.appendChild(iconEl);
        addedElements.push({ id, type: 'icon', content: selectedIcon });

        // make it draggable immediately
        makeDraggable(iconEl);
    }

    //  if (lang === 'fr-CA') {     await updateFrenchPreviewOnly();   }

      reapplyInteractivity();
      updateElementList();
      await applyTranslation();
    }



    function makeSelectable(el) {
        el.addEventListener("mousedown", e => {
            if (currentMode === "edit") {
                selectElement(el);
                e.stopPropagation();
            }
        });
    }

function selectElement(el) {
    // remove old highlight
    if (selectionBox) {
        selectionBox.remove();
        selectionBox = null;
    }

    // clear old selected class
    if (selectedElement) {
        selectedElement.classList.remove("selected-element");
    }

    selectedElement = el;
    selectedElement.classList.add("selected-element");

    // ---- CREATE SELECTION HIGHLIGHT RECT ----
    const parent = el.parentNode; // <-- insert inside parent, not always svg
    const bbox = el.getBBox();

    const bgColor = document.getElementById('bgColorText').value;
    const borderColor = document.getElementById('borderColorText').value;
    const borderWidth = parseFloat(document.getElementById('borderWidth').value);
    const borderRadius = parseFloat(document.getElementById('borderRadius').value);

    selectionBox = document.createElementNS("http://www.w3.org/2000/svg", "rect");
    selectionBox.setAttribute("x", bbox.x - borderWidth);
    selectionBox.setAttribute("y", bbox.y - borderWidth);
    selectionBox.setAttribute("width", bbox.width + borderWidth * 2);
    selectionBox.setAttribute("height", bbox.height + borderWidth * 2);
    selectionBox.setAttribute("fill", bgColor);
    selectionBox.setAttribute("stroke", borderColor);
    selectionBox.setAttribute("stroke-width", borderWidth);
    // Keep selection helper rectangular so it doesn't inherit canvas rounding
    selectionBox.setAttribute("rx", 0);
    selectionBox.setAttribute("pointer-events", "none");



    // insert directly before element to sit behind it
    parent.insertBefore(selectionBox, el);
}


    // ---------- ERASE MODE ----------
    function makeElementsErasable() {
      const svg = document.querySelector('#svgPreview svg');
      if (!svg) return;
      svg.querySelectorAll('text, circle, rect, path, polygon, line, ellipse, polyline, image').forEach(el => {
        if (!el.hasAttribute('data-background')) {
          el.classList.add('erasable-element');
          el.onclick = e => {
            if (currentMode === 'erase') {
              e.stopPropagation();
              eraseElement(el);
            }
          };
        }
      });
    }

    function removeErasableClasses() {
      document.querySelectorAll('.erasable-element').forEach(el => {
        el.classList.remove('erasable-element');
        el.onclick = null;
      });
    }

    function eraseElement(el) {
      if (confirm('Delete this element?')) {
        const id = el.getAttribute('data-element-id');
        if (id) {
          addedElements = addedElements.filter(item => item.id !== id);
        }
        el.remove();
        updateElementList();
      }
    }

    // ---------- ELEMENT LIST ----------
    function updateElementList() {
      const container = document.getElementById('elementList');
      if (addedElements.length === 0) {
        container.innerHTML = '<p style="color: #999; text-align: center; font-size: 12px;">No elements added yet</p>';
        return;
      }
      container.innerHTML = '';
      addedElements.forEach(item => {
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
      addedElements = addedElements.filter(item => item.id !== id);
      updateElementList();
    };

    function clearAllElements() {
      if (addedElements.length === 0) {
        alert('No elements to clear!');
        return;
      }
      if (confirm('Clear all added elements?')) {
        addedElements.forEach(item => {
          const el = document.querySelector(`[data-element-id="${item.id}"]`);
          if (el) el.remove();
        });
        addedElements = [];
        updateElementList();
      }
    }

    // ---------- DRAG FUNCTIONALITY ----------
    function makeDraggable(el) {
      if (el.hasAttribute('data-background')) return;

      el.style.cursor = 'move';
      el.addEventListener('mousedown', function (e) {
        if (currentMode !== 'edit') return;
        e.preventDefault();
        dragged = el;

        const svg = el.ownerSVGElement;
        const pt = svg.createSVGPoint();
        const rect = el.getBBox();

        pt.x = e.clientX;
        pt.y = e.clientY;
        const cursorPt = pt.matrixTransform(svg.getScreenCTM().inverse());

        offset.x = cursorPt.x - rect.x - (rect.width / 2);
        offset.y = cursorPt.y - rect.y - (rect.height / 2);
      });
    }

    document.addEventListener('mousemove', function (e) {
      if (!dragged) return;

      const svg = dragged.ownerSVGElement;
      const pt = svg.createSVGPoint();
      pt.x = e.clientX;
      pt.y = e.clientY;
      const cursorPt = pt.matrixTransform(svg.getScreenCTM().inverse());

      const newX = cursorPt.x - offset.x;
      const newY = cursorPt.y - offset.y;

      if (dragged.tagName === 'text') {
        // Check if it has tspans (multiline text) or is single line
        const hasMultiline = dragged.querySelector('tspan');
        
        if (hasMultiline) {
          // For multiline text, update x and y attributes
          dragged.setAttribute('x', newX);
          dragged.setAttribute('y', newY);
          
          // Update all tspans to have the same x coordinate
          dragged.querySelectorAll('tspan').forEach(tspan => {
            tspan.setAttribute('x', newX);
          });
        } else {
          // For single line text, just update x and y
          dragged.setAttribute('x', newX);
          dragged.setAttribute('y', newY);
        }
      } else if (dragged.tagName === 'g') {
        // For groups, update transform
        dragged.setAttribute('transform', `translate(${newX}, ${newY})`);
      } else if (dragged.tagName === 'image') {
        // For images set x/y directly
        dragged.setAttribute('x', newX);
        dragged.setAttribute('y', newY);
      }
    });

    document.addEventListener('mouseup', function () {
      dragged = null;
    });

    function reapplyInteractivity() {
      const svg = document.querySelector('#svgPreview svg');
      if (!svg) return;

      // --- REMOVE ALL ERASE HANDLERS IN EDIT MODE ---
      if (currentMode === "edit") {
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


      if (currentMode === 'erase') {
        makeElementsErasable();
      }
    }

    function updateSVGViewBox() {
        const svg = document.querySelector('#svgPreview svg');
        if (!svg) return;

        const newW = parseFloat(document.getElementById('widthInput').value);
        const newH = parseFloat(document.getElementById('heightInput').value);

        // Update BOTH viewBox and physical size
        svg.setAttribute('viewBox', `0 0 ${newW} ${newH}`);
        svg.setAttribute('width', String(newW));
        svg.setAttribute('height', String(newH));
        svg.style.width = `${newW}px`;
        svg.style.height = `${newH}px`;
    }


    function resizeInternalShapes() {
        const svg = document.querySelector('#svgPreview svg');
        if (!svg) return;

        const newW = parseFloat(document.getElementById('widthInput').value);
        const newH = parseFloat(document.getElementById('heightInput').value);

        // Change the main rect with fixed size (your example)
        const rect = svg.querySelector('#Rectangle_1756 rect');
        if (rect) {
            rect.setAttribute('width', newW);
            rect.setAttribute('height', newH);
        }
    }

    // ---------- DOWNLOAD SVG----------
    function downloadSVG() {
      const svg = document.querySelector('#svgPreview svg');
      const container = document.getElementById('svgPreview');
      if (!svg || !container) return alert('No SVG to download!');
      // Remove any transient selection highlight from the live preview before cloning
      if (typeof selectionBox !== 'undefined' && selectionBox) {
        selectionBox.remove();
        selectionBox = null;
      }
      // Get current settings
      const inputInnerW = parseFloat(document.getElementById('widthInput').value) || 500;
      const inputInnerH = parseFloat(document.getElementById('heightInput').value) || 500;
      const borderW = parseFloat(document.getElementById('borderWidth').value) || 0;
      const borderR = parseFloat(document.getElementById('borderRadius').value) || 0;
      const borderColor = document.getElementById('borderColorText').value || '#000';
      const bgColor = document.getElementById('bgColorText').value || '#fff';
 
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
      const frenchBox = document.getElementById('frenchCtaBox');
      if (frenchBox && frenchBox.style.display !== 'none') {
        const frenchSvg = document.querySelector('#svgFrenchPreview svg');
        if (frenchSvg) {
          generateExportSVG(frenchSvg, 'edited-svg-fr.svg');
        }
      }
    }

    // Add this helper to produce a standalone SVG string suitable for saving/downloading.
function exportSVGSource() {
    const svg = document.querySelector('#svgPreview svg');
    if (!svg) return null;

    // Gather settings (fall back to SVG's attributes)
    const inputInnerW = parseFloat(document.getElementById('widthInput').value) || parseFloat(svg.getAttribute('width')) || 500;
    const inputInnerH = parseFloat(document.getElementById('heightInput').value) || parseFloat(svg.getAttribute('height')) || 500;
    const borderW = parseFloat(document.getElementById('borderWidth').value) || 0;
    const borderR = parseFloat(document.getElementById('borderRadius').value) || 0;
    const borderColor = document.getElementById('borderColorText').value || '#000';
    const bgColor = document.getElementById('bgColorText').value || '#fff';

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

    // Move children into an inner group offset by border width
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
        if (!document.getElementById("svgPreview").contains(e.target)) {
            if (selectedElement) {
                selectedElement.classList.remove("selected-element");
                selectedElement = null;
            }
            if (selectionBox) {
                selectionBox.remove();
                selectionBox = null;
            }
        }
    });

    // ---------- INITIALISE ----------
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelector('.mode-btn:not(.erase-mode)').classList.add('active');
      currentMode = 'edit';
 
      // Live updates
      ['widthInput', 'heightInput', 'borderWidth', 'borderRadius', 'borderColorText', 'bgColorText','iconSize'].forEach(id => {
        const el = document.getElementById(id);
         if (el) el.addEventListener('input', () => {
            applyVisualStyles();
            updateSVGViewBox();
            resizeInternalShapes();
            try { updateUploadedImagesSize(); } catch (err) {}
        });
      });
 
      // Text-related live preview wiring
      ['textContent','textX','textY','textSize','fontFamily','fontWeight','fontStyle','letterSpacing','lineHeight','textMode','textWrapWidth','textColorText','textColorPicker'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        const ev = el.tagName.toLowerCase() === 'select' || el.type === 'file' ? 'change' : 'input';
        el.addEventListener(ev, () => {
          try { updatePreviewText(); } catch (e) {}
        });
      });
 
      // initial preview run
      try { updatePreviewText(); } catch (e) {}
    });


  






























    // BUTTON TOGGLE FOR TEXT PREVIEW MODES
    //  SVG UTILITY FUNCTIONS
    // let currentSVG = null;
    // let selectedIcon = '';
    // let currentMode = 'edit';
    // let addedElements = [];
    // let originalTexts = new WeakMap();
    // let dragged = null;
    // let offset = { x: 0, y: 0 };
    // let currentViewBox = { w: 500, h: 500 };
    // let selectedElement = null;
    // let selectionBox = null;


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
    document.getElementById('borderColorPicker').addEventListener('input', e => {
      document.getElementById('borderColorText').value = e.target.value;
      // update live visuals when user uses the color picker
      try { applyVisualStyles(); } catch (err) {}
    });
    document.getElementById('borderColorText').addEventListener('input', e => {
      document.getElementById('borderColorPicker').value = e.target.value;
      try { applyVisualStyles(); } catch (err) {}
    });
    document.getElementById('textColorPicker').addEventListener('input', e => {
      document.getElementById('textColorText').value = e.target.value;
      try { updatePreviewText(); } catch (err) {}
    });
    document.getElementById('textColorText').addEventListener('input', e => {
      document.getElementById('textColorPicker').value = e.target.value;
      try { updatePreviewText(); } catch (err) {}
    });
    document.getElementById('bgColorPicker').addEventListener('input', e => {
      document.getElementById('bgColorText').value = e.target.value;
      try { applyVisualStyles(); } catch (err) {}
    });
    document.getElementById('bgColorText').addEventListener('input', e => {
      document.getElementById('bgColorPicker').value = e.target.value;
      try { applyVisualStyles(); } catch (err) {}
    });

    // ---------- ICON IMAGE UPLOAD (multiple images, default placement, draggable) ----------
    // We'll accept multiple files from #iconUpload and insert each as an <image> into the current svg.
    const uploadInput = document.getElementById("iconUpload");
    const iconX = document.getElementById("iconX");
    const iconY = document.getElementById("iconY");
    const iconSize = document.getElementById("iconSize");

    uploadInput.addEventListener("change", function () {
        const files = Array.from(this.files || []);
        if (!files.length) return;

        // Insert each file
        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                const base64 = e.target.result;
                insertUploadedImage(base64, file.name);
            };
            reader.readAsDataURL(file);
        });

        // clear the input so same file can be picked again if needed
        this.value = '';
    });

    // Insert image into current SVG with default px placement, convert to viewBox units
    function insertUploadedImage(base64url, filename) {
        const svg = document.querySelector('#svgPreview svg');
        if (!svg) {
            console.error('No SVG available to insert image into.');
            return;
        }

        // Ensure xlink namespace exists
        if (!svg.getAttribute('xmlns:xlink')) svg.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');

        // default pixel placement (Option A)
        const defaultXpx = parseFloat(iconX.value) || 50;   // we still respect the default inputs if user changed them
        const defaultYpx = parseFloat(iconY.value) || 50;
        const defaultWpx = parseFloat(iconSize.value) || 120;

        // Convert px to viewBox units
        const viewBox = svg.getAttribute('viewBox').split(/\s+/).map(Number);
        const vbWidth = viewBox[2];
        const vbHeight = viewBox[3];

        // container (visible) pixel size
        // Use the SVG's actual rendered pixel size to compute scale so
        // editor chrome (borders) applied to the container don't change
        // the conversion from px -> viewBox units.
        const svgRect = svg.getBoundingClientRect();
        const containerW = svgRect.width || svg.parentElement.clientWidth || vbWidth;
        const containerH = svgRect.height || svg.parentElement.clientHeight || vbHeight;

        // scale to convert px -> viewBox units (use width-based scale)
        const scale = vbWidth / (containerW || vbWidth);

        const x_vb = defaultXpx * scale;
        const y_vb = defaultYpx * scale;
        const w_vb = defaultWpx * scale;

        // Create SVG <image>
        const imgEl = document.createElementNS('http://www.w3.org/2000/svg', 'image');
        imgEl.setAttributeNS('http://www.w3.org/1999/xlink', 'href', base64url);

        // To preserve aspect ratio, we need natural image dimensions — create an Image object
        const tmpImg = new Image();
        tmpImg.onload = function() {
            const natW = tmpImg.naturalWidth || 1;
            const natH = tmpImg.naturalHeight || 1;
            const aspect = natH / natW;
            const h_vb = w_vb * aspect;

            imgEl.setAttribute('x', String(x_vb));
            imgEl.setAttribute('y', String(y_vb));
            imgEl.setAttribute('width', String(w_vb));
            imgEl.setAttribute('height', String(h_vb));
            imgEl.setAttribute('preserveAspectRatio', 'xMidYMid meet');

            const id = 'img-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
            imgEl.setAttribute('data-element-id', id);

          // store natural size and base px width so we can update size later
          imgEl.setAttribute('data-natw', String(natW));
          imgEl.setAttribute('data-nath', String(natH));
          imgEl.setAttribute('data-base-width-px', String(defaultWpx));

            svg.appendChild(imgEl);

            // Record in addedElements list
            addedElements.push({ id, type: 'image', content: filename || 'uploaded-image' });

            // Make interactive
            reapplyInteractivity();
            updateElementList();
        };
        tmpImg.src = base64url;
    }

    // ---------- LANGUAGE CHANGE ----------
    // When language changes, translate existing SVG texts AND update
    // the live textarea preview (without overwriting user input).
    document.getElementById('languageSelect').addEventListener('change', async () => {
      await applyTranslation();
      try {
        const lang = document.getElementById('languageSelect').value;
        const raw = (document.getElementById('textContent').value || '').trim();
        if (raw) {
          const translated = await translateText(raw, lang);
          updatePreviewText(translated);
        }
      } catch (err) { console.error(err); }
    });

    // ---------- TEXT MODE TOGGLE ----------
    document.getElementById('textMode').addEventListener('change', function(e) {
      const multilineOptions = document.getElementById('multilineOptions');
      if (e.target.value === 'multi') {
        multilineOptions.style.display = 'block';
      } else {
        multilineOptions.style.display = 'none';
      }
    });

    // ---------- MODE SWITCH ----------
    function setMode(mode, btn) {
      currentMode = mode;
      document.querySelectorAll('.mode-btn').forEach(btn => btn.classList.remove('active'));
      btn.classList.add('active');
      const svg = document.querySelector('#svgPreview svg');
      if (svg) {
        if (mode === 'erase') {
          svg.classList.add('erase-cursor');
          makeElementsErasable();
        } else {
          svg.classList.remove('erase-cursor');
          removeErasableClasses();
        }
      }
      reapplyInteractivity();
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
          applyTo('#svgPreview text, #svgPreview tspan');
          applyTo('#htmlPreview text, #htmlPreview tspan');
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
      if (!currentSVG) return;

      const parser = new DOMParser();
      const svgDoc = parser.parseFromString(currentSVG, 'image/svg+xml');
      const svgElement = svgDoc.documentElement;

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
        bgRect.setAttribute('fill', document.getElementById('bgColorText').value);
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
        if (!originalTexts.has(text)) {
          originalTexts.set(text, text.textContent.trim());
        }
      });

      applyVisualStyles();
      reapplyInteractivity();
      addedElements = [];
      updateElementList();
      // update live preview text if user is typing
      try { updatePreviewText(); } catch (e) { /* ignore if preview not ready */ }
    }

    // ---------- UPDATE UPLOADED IMAGES SIZE ----------
    function updateUploadedImagesSize() {
      const svg = document.querySelector('#svgPreview svg');
      if (!svg) return;

      const images = Array.from(svg.querySelectorAll('image[data-natw]'));
      if (!images.length) return;

      const iconSizePx = parseFloat(document.getElementById('iconSize').value) || 120;

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
    function applyVisualStyles() {
      const svg = document.querySelector('#svgPreview svg');
      if (!svg) return;

      const width = parseFloat(document.getElementById('widthInput').value);
      const height = parseFloat(document.getElementById('heightInput').value);
      const borderColor = document.getElementById('borderColorText').value;
      const borderWidth = parseFloat(document.getElementById('borderWidth').value);
      const borderRadius = parseFloat(document.getElementById('borderRadius').value);
      const bgColor = document.getElementById('bgColorText').value;

    const container = document.getElementById('svgPreview');
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

    async function applyTranslation() {
      const svg = document.querySelector('#svgPreview svg');
      if (!svg) return;

      const lang = document.getElementById('languageSelect').value;
      const texts = svg.querySelectorAll('text');

      for (const txt of texts) {
        // Prefer the stored original text if available; otherwise use the
        // current node text as the source (this handles text nodes that
        // were created after the original mapping was made).
        const storedOrig = originalTexts.get(txt);
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
        try { createAlternatePreview(); } catch (e) { console.error(e); }
      } else {
        removeAlternatePreview();
      }
    }

    // Create an alternate preview SVG showing original English texts.
    function createAlternatePreview() {
      // remove existing if any
      removeAlternatePreview();
      const liveSvg = document.querySelector('#svgPreview svg');
      if (!liveSvg) return;

      // Collect original texts in order from the live DOM
      const liveTexts = Array.from(liveSvg.querySelectorAll('text'));
      const originals = liveTexts.map(t => originalTexts.get(t) || (t.textContent || '').trim());

      // Clone the live SVG and replace text nodes with originals
      const altSvg = liveSvg.cloneNode(true);
      const altTexts = Array.from(altSvg.querySelectorAll('text'));
      altTexts.forEach((t, i) => {
        if (originals[i]) t.textContent = originals[i];
      });

      // Wrap in a container for labeling
      const altContainer = document.createElement('div');
      altContainer.id = 'svgPreviewAlt';
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

    function removeAlternatePreview() {
      const existing = document.getElementById('svgPreviewAlt');
      if (existing && existing.parentNode) existing.parentNode.removeChild(existing);
    }

    // ---------- LIVE PREVIEW FOR TEXT INPUTS ----------
    function updatePreviewText(translatedOverride) {
      
      const svg = document.querySelector('#svgPreview svg');
      if (!svg){
       
         return;
      }else{
       
      }

      // remove any existing transient preview element
      const existing = svg.querySelector('[data-preview="true"]');
      if (existing) existing.remove();
      const rawText = (typeof translatedOverride === 'string' && translatedOverride !== undefined)
        ? translatedOverride
        : (document.getElementById('textContent').value || '');
      if (!rawText.trim()) return; // nothing to preview

      const textMode = document.getElementById('textMode').value;
      const size = parseFloat(document.getElementById('textSize').value) || 16;
      const color = document.getElementById('textColorText').value || '#000';
      const fontFamily = document.getElementById('fontFamily').value;
      const fontWeight = document.getElementById('fontWeight').value;
      const fontStyle = document.getElementById('fontStyle').value;
      const letterSpacing = document.getElementById('letterSpacing').value;
      const lineHeight = document.getElementById('lineHeight').value;
      const x = document.getElementById('textX').value;
      const y = document.getElementById('textY').value;
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
        const wrapWidth = parseFloat(document.getElementById('textWrapWidth').value) || 300;
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
            const offset = -totalHeight / 2;
            if (offset !== 0) tspan.setAttribute('dy', offset);
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
    async function applyChanges() {
      if (!currentSVG) {
        alert('Please upload an SVG file first!');
        return;
      }

      const svg = document.querySelector('#svgPreview svg');
      if (!svg) return;

      // CRITICAL: Remove any live preview text before applying permanent changes
      svg.querySelectorAll('[data-preview="true"]').forEach(el => el.remove());
      
      applyVisualStyles();

      const lang = document.getElementById('languageSelect').value;

      // ---- ADD TEXT ----
      const rawText = document.getElementById('textContent').value.trim();
      if (rawText) {
        const translated = await translateText(rawText, lang);
        const size = document.getElementById('textSize').value;
        const color = document.getElementById('textColorText').value;
        const fontFamily = document.getElementById('fontFamily').value;
        const fontWeight = document.getElementById('fontWeight').value;
        const fontStyle = document.getElementById('fontStyle').value;
        const letterSpacing = document.getElementById('letterSpacing').value;
        const lineHeight = document.getElementById('lineHeight').value;
        const textMode = document.getElementById('textMode').value;
        const x = document.getElementById('textX').value;
        const y = document.getElementById('textY').value;
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
           originalTexts.set(textEl, englishText);  // ← Store original 
           //          originalTexts.set(textEl, rawText.trim());

          svg.appendChild(textEl);
          addedElements.push({ id, type: 'text', content: translated });
        } else {
          // ---- MULTILINE TEXT ----
          const wrapWidth = parseFloat(document.getElementById('textWrapWidth').value);
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
              const offset = -totalHeight / 2;
              if (offset !== 0) {
                tspan.setAttribute('dy', offset);
              }
            } else {
              tspan.setAttribute('dy', lineSpacing);
            }
            
            tspan.textContent = line;
            textEl.appendChild(tspan);
          });
          
          svg.appendChild(textEl);
          addedElements.push({ id: textId, type: 'multiline-text', content: translated });
          originalTexts.set(textEl, rawText.trim());
          
          // Make the text element draggable
          makeDraggable(textEl);
        }
      }

      // ADD ICON (text-based icon or uploaded images handled elsewhere)
      if (selectedIcon) {
        const size = document.getElementById('iconSize').value;
        const xPx = document.getElementById('iconX').value;   // user input (px)
        const yPx = document.getElementById('iconY').value;   // user input (px)

        // ---- NEW: convert px → viewBox coordinates -----------------
        const svgEl = document.querySelector('#svgPreview svg');
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
        iconEl.textContent = selectedIcon;

        const id = 'icon-' + Date.now();
        iconEl.setAttribute('data-element-id', id);   // <-- needed for drag & erase

        svgEl.appendChild(iconEl);
        addedElements.push({ id, type: 'icon', content: selectedIcon });

        // make it draggable immediately
        makeDraggable(iconEl);
    }

    //  if (lang === 'fr-CA') {     await updateFrenchPreviewOnly();   }

      reapplyInteractivity();
      updateElementList();
      await applyTranslation();
    }



    function makeSelectable(el) {
        el.addEventListener("mousedown", e => {
            if (currentMode === "edit") {
                selectElement(el);
                e.stopPropagation();
            }
        });
    }

function selectElement(el) {
    // remove old highlight
    if (selectionBox) {
        selectionBox.remove();
        selectionBox = null;
    }

    // clear old selected class
    if (selectedElement) {
        selectedElement.classList.remove("selected-element");
    }

    selectedElement = el;
    selectedElement.classList.add("selected-element");

    // ---- CREATE SELECTION HIGHLIGHT RECT ----
    const parent = el.parentNode; // <-- insert inside parent, not always svg
    const bbox = el.getBBox();

    const bgColor = document.getElementById('bgColorText').value;
    const borderColor = document.getElementById('borderColorText').value;
    const borderWidth = parseFloat(document.getElementById('borderWidth').value);
    const borderRadius = parseFloat(document.getElementById('borderRadius').value);

    selectionBox = document.createElementNS("http://www.w3.org/2000/svg", "rect");
    selectionBox.setAttribute("x", bbox.x - borderWidth);
    selectionBox.setAttribute("y", bbox.y - borderWidth);
    selectionBox.setAttribute("width", bbox.width + borderWidth * 2);
    selectionBox.setAttribute("height", bbox.height + borderWidth * 2);
    selectionBox.setAttribute("fill", bgColor);
    selectionBox.setAttribute("stroke", borderColor);
    selectionBox.setAttribute("stroke-width", borderWidth);
    // Keep selection helper rectangular so it doesn't inherit canvas rounding
    selectionBox.setAttribute("rx", 0);
    selectionBox.setAttribute("pointer-events", "none");



    // insert directly before element to sit behind it
    parent.insertBefore(selectionBox, el);
}


    // ---------- ERASE MODE ----------
    function makeElementsErasable() {
      const svg = document.querySelector('#svgPreview svg');
      if (!svg) return;
      svg.querySelectorAll('text, circle, rect, path, polygon, line, ellipse, polyline, image').forEach(el => {
        if (!el.hasAttribute('data-background')) {
          el.classList.add('erasable-element');
          el.onclick = e => {
            if (currentMode === 'erase') {
              e.stopPropagation();
              eraseElement(el);
            }
          };
        }
      });
    }

    function removeErasableClasses() {
      document.querySelectorAll('.erasable-element').forEach(el => {
        el.classList.remove('erasable-element');
        el.onclick = null;
      });
    }

    function eraseElement(el) {
      if (confirm('Delete this element?')) {
        const id = el.getAttribute('data-element-id');
        if (id) {
          addedElements = addedElements.filter(item => item.id !== id);
        }
        el.remove();
        updateElementList();
      }
    }

    // ---------- ELEMENT LIST ----------
    function updateElementList() {
      const container = document.getElementById('elementList');
      if (addedElements.length === 0) {
        container.innerHTML = '<p style="color: #999; text-align: center; font-size: 12px;">No elements added yet</p>';
        return;
      }
      container.innerHTML = '';
      addedElements.forEach(item => {
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
      addedElements = addedElements.filter(item => item.id !== id);
      updateElementList();
    };

    function clearAllElements() {
      if (addedElements.length === 0) {
        alert('No elements to clear!');
        return;
      }
      if (confirm('Clear all added elements?')) {
        addedElements.forEach(item => {
          const el = document.querySelector(`[data-element-id="${item.id}"]`);
          if (el) el.remove();
        });
        addedElements = [];
        updateElementList();
      }
    }

    // ---------- DRAG FUNCTIONALITY ----------
    function makeDraggable(el) {
      if (el.hasAttribute('data-background')) return;

      el.style.cursor = 'move';
      el.addEventListener('mousedown', function (e) {
        if (currentMode !== 'edit') return;
        e.preventDefault();
        dragged = el;

        const svg = el.ownerSVGElement;
        const pt = svg.createSVGPoint();
        const rect = el.getBBox();

        pt.x = e.clientX;
        pt.y = e.clientY;
        const cursorPt = pt.matrixTransform(svg.getScreenCTM().inverse());

        offset.x = cursorPt.x - rect.x - (rect.width / 2);
        offset.y = cursorPt.y - rect.y - (rect.height / 2);
      });
    }

    document.addEventListener('mousemove', function (e) {
      if (!dragged) return;

      const svg = dragged.ownerSVGElement;
      const pt = svg.createSVGPoint();
      pt.x = e.clientX;
      pt.y = e.clientY;
      const cursorPt = pt.matrixTransform(svg.getScreenCTM().inverse());

      const newX = cursorPt.x - offset.x;
      const newY = cursorPt.y - offset.y;

      if (dragged.tagName === 'text') {
        // Check if it has tspans (multiline text) or is single line
        const hasMultiline = dragged.querySelector('tspan');
        
        if (hasMultiline) {
          // For multiline text, update x and y attributes
          dragged.setAttribute('x', newX);
          dragged.setAttribute('y', newY);
          
          // Update all tspans to have the same x coordinate
          dragged.querySelectorAll('tspan').forEach(tspan => {
            tspan.setAttribute('x', newX);
          });
        } else {
          // For single line text, just update x and y
          dragged.setAttribute('x', newX);
          dragged.setAttribute('y', newY);
        }
      } else if (dragged.tagName === 'g') {
        // For groups, update transform
        dragged.setAttribute('transform', `translate(${newX}, ${newY})`);
      } else if (dragged.tagName === 'image') {
        // For images set x/y directly
        dragged.setAttribute('x', newX);
        dragged.setAttribute('y', newY);
      }
    });

    document.addEventListener('mouseup', function () {
      dragged = null;
    });

    function reapplyInteractivity() {
      const svg = document.querySelector('#svgPreview svg');
      if (!svg) return;

      // --- REMOVE ALL ERASE HANDLERS IN EDIT MODE ---
      if (currentMode === "edit") {
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


      if (currentMode === 'erase') {
        makeElementsErasable();
      }
    }

    function updateSVGViewBox() {
        const svg = document.querySelector('#svgPreview svg');
        if (!svg) return;

        const newW = parseFloat(document.getElementById('widthInput').value);
        const newH = parseFloat(document.getElementById('heightInput').value);

        // Update BOTH viewBox and physical size
        svg.setAttribute('viewBox', `0 0 ${newW} ${newH}`);
        svg.setAttribute('width', String(newW));
        svg.setAttribute('height', String(newH));
        svg.style.width = `${newW}px`;
        svg.style.height = `${newH}px`;
    }


    function resizeInternalShapes() {
        const svg = document.querySelector('#svgPreview svg');
        if (!svg) return;

        const newW = parseFloat(document.getElementById('widthInput').value);
        const newH = parseFloat(document.getElementById('heightInput').value);

        // Change the main rect with fixed size (your example)
        const rect = svg.querySelector('#Rectangle_1756 rect');
        if (rect) {
            rect.setAttribute('width', newW);
            rect.setAttribute('height', newH);
        }
    }

    // ---------- DOWNLOAD SVG----------
    function downloadSVG() {
      const svg = document.querySelector('#svgPreview svg');
      const container = document.getElementById('svgPreview');
      if (!svg || !container) return alert('No SVG to download!');
      // Remove any transient selection highlight from the live preview before cloning
      if (typeof selectionBox !== 'undefined' && selectionBox) {
        selectionBox.remove();
        selectionBox = null;
      }
      // Get current settings
      const inputInnerW = parseFloat(document.getElementById('widthInput').value) || 500;
      const inputInnerH = parseFloat(document.getElementById('heightInput').value) || 500;
      const borderW = parseFloat(document.getElementById('borderWidth').value) || 0;
      const borderR = parseFloat(document.getElementById('borderRadius').value) || 0;
      const borderColor = document.getElementById('borderColorText').value || '#000';
      const bgColor = document.getElementById('bgColorText').value || '#fff';
 
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
      const frenchBox = document.getElementById('frenchCtaBox');
      if (frenchBox && frenchBox.style.display !== 'none') {
        const frenchSvg = document.querySelector('#svgFrenchPreview svg');
        if (frenchSvg) {
          generateExportSVG(frenchSvg, 'edited-svg-fr.svg');
        }
      }
    }

    // Add this helper to produce a standalone SVG string suitable for saving/downloading.
function exportSVGSource() {
    const svg = document.querySelector('#svgPreview svg');
    if (!svg) return null;

    // Gather settings (fall back to SVG's attributes)
    const inputInnerW = parseFloat(document.getElementById('widthInput').value) || parseFloat(svg.getAttribute('width')) || 500;
    const inputInnerH = parseFloat(document.getElementById('heightInput').value) || parseFloat(svg.getAttribute('height')) || 500;
    const borderW = parseFloat(document.getElementById('borderWidth').value) || 0;
    const borderR = parseFloat(document.getElementById('borderRadius').value) || 0;
    const borderColor = document.getElementById('borderColorText').value || '#000';
    const bgColor = document.getElementById('bgColorText').value || '#fff';

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

    // Move children into an inner group offset by border width
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
        if (!document.getElementById("svgPreview").contains(e.target)) {
            if (selectedElement) {
                selectedElement.classList.remove("selected-element");
                selectedElement = null;
            }
            if (selectionBox) {
                selectionBox.remove();
                selectionBox = null;
            }
        }
    });

    // ---------- INITIALISE ----------
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelector('.mode-btn:not(.erase-mode)').classList.add('active');
      currentMode = 'edit';
 
      // Live updates
      ['widthInput', 'heightInput', 'borderWidth', 'borderRadius', 'borderColorText', 'bgColorText','iconSize'].forEach(id => {
        const el = document.getElementById(id);
         if (el) el.addEventListener('input', () => {
            applyVisualStyles();
            updateSVGViewBox();
            resizeInternalShapes();
            try { updateUploadedImagesSize(); } catch (err) {}
        });
      });
 
      // Text-related live preview wiring
      ['textContent','textX','textY','textSize','fontFamily','fontWeight','fontStyle','letterSpacing','lineHeight','textMode','textWrapWidth','textColorText','textColorPicker'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        const ev = el.tagName.toLowerCase() === 'select' || el.type === 'file' ? 'change' : 'input';
        el.addEventListener(ev, () => {
          try { updatePreviewText(); } catch (e) {}
        });
      });
 
      // initial preview run
      try { updatePreviewText(); } catch (e) {}
    });


// HTML UTILITY FUNCTIONS




