<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Shape Layout Visualizer</h1>
     
    </section>

    <section class="content">
      <!-- Mobile Instructions Banner - Only shows on small screens -->
<div class="mobile-instructions alert alert-info visible-xs">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
  <h4><i class="icon fa fa-info"></i> Mobile Instructions</h4>
  <ul>
    <li><strong>Drag shapes</strong>: Tap and move to drag shapes</li>
    <li><strong>Delete shapes</strong>: Long press (tap and hold) on a shape to delete</li>
  </ul>
</div>
      <div class="row">
        <div class="col-md-3">
          <!-- Shape Configuration Box -->
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Shape Configuration</h3>
            </div>
            <div class="box-body">
              <form id="shapeConfig">
                <div class="form-group">
                  <label>Shape Type:</label>
                  
                  <select class="form-control" id="shapeType">
                  <option value="" disabled selected>Select Shape</option>
                  <option value="custom">Custom Polygon</option>
                    <option value="rectangle">Rectangle</option>
                    <option value="square">Square</option>
                    <option value="circle">Circle</option>
                    <option value="ellipse">Ellipse</option>
                    <option value="triangle">Triangle</option>
                    <option value="invertedTriangle">Inverted Triangle</option>
                    <option value="rightTriangle">Right Triangle</option>
                    <option value="pentagon">Pentagon</option>
                    <option value="hexagon">Hexagon</option>
                    <option value="octagon">Octagon</option>
                    <option value="trapezoid">Trapezoid</option>
                    <option value="parallelogram">Parallelogram</option>
                    <option value="rhombus">Rhombus</option>
                    <option value="star">Star</option>
                    <option value="arrow">Arrow</option>
                    <option value="cross">Cross</option>
              
                  </select>
                </div>
<!-- Replace existing width/height form groups with this -->
<div id="standardDimensions">
  <div class="form-group">
    <label>Width (mm):</label>
    <input type="number" class="form-control" id="shapeWidth" min="25" max="1220" value="305">
  </div>
  <div class="form-group">
    <label>Height (mm):</label>
    <input type="number" class="form-control" id="shapeHeight" min="25" max="2440" value="305">
  </div>
</div>
                <div class="form-group">
                  <label>Color:</label>
                  <input type="color" class="form-control" id="shapeColor" value="#3498db">
                </div>
                <div class="form-group">
  <label>Rotation (degrees):</label>
  <div class="input-group">
    <input type="number" class="form-control" id="rotation" min="0" max="359" value="0">
    <span class="input-group-addon">°</span>
  </div>
  <small class="form-text text-muted">Enter a value between 0-359 degrees</small>
</div>
                <div class="form-group">
                  <label>Quantity:</label>
                  <input type="number" class="form-control" id="quantity" min="1" value="1">
                </div>
                <div class="form-group" id="customShapeOptions" style="display: none;">
  <label>Number of Sides:</label>
  <input type="number" class="form-control" id="shapeSides" min="3" max="20" value="5">
  <small class="form-text text-muted">Min: 3, Max: 20 sides</small>
   
  <!-- Add this div to contain the side length inputs -->
  <div id="sideLengthsContainer" class="mt-3">
    <!-- Side length inputs will be dynamically added here -->
  </div>
</div>
              </form>
            </div>
            <div class="box-footer">
              <button class="btn btn-primary btn-block" onclick="addShapes()">Add Shapes</button>
            </div>
          </div>
          
  
        </div>

        <div class="col-md-9">
   <!-- Canvas Container -->
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">1220 mm x 2440 mm Layout</h3>
    <div class="box-tools pull-right">
      <button class="btn btn-sm btn-danger" onclick="clearCanvases()">Clear All</button>
      <button class="btn btn-sm btn-info" onclick="toggleGrid()">Toggle Grid</button>
      <button class="btn btn-sm btn-success" onclick="createNewCanvas()">Add Canvas</button>
      <button class="btn btn-sm btn-warning" onclick="removeLastCanvas()">Remove Canvas</button>
      <button class="btn btn-sm btn-primary" onclick="duplicateSelectedShape()">Duplicate</button>

    </div>
    <!-- Moving stats here, above the canvas -->
    <div id="layoutStats" style="margin-top: 10px; clear: both; font-size: 18px;">
      <b>Total Shapes: <span id="shapeCount">0</span> | </b>
      <b> Total Area Used: <span id="areaUsed">0</span> sq mm |</b>
      <b>Material Efficiency: <span id="efficiency">0</span>%</b>
      <!-- Shape Rotation Controls (when shape is selected) -->
<div class="box" id="rotationControls" style="display: none;">
  <div class="box-header with-border">
    <h3 class="box-title">Shape Rotation</h3>
  </div>
  <div class="box-body">
    <div class="form-group">
      <label>Rotate Selected Shape:</label>
      <div class="input-group">
        <input type="number" class="form-control" id="selectedShapeRotation" min="0" max="359" value="0">
        <span class="input-group-addon">°</span>
      </div>
    </div>
  </div>
  <div class="box-footer">
    <button class="btn btn-primary btn-block" onclick="applyRotationToSelected()">Apply Rotation</button>
  </div>
</div>
    </div>
  </div>
  <div class="box-body" id="canvasContainer">
    <div class="canvas-wrapper">
      <div class="canvas" data-sheet="1" style="width: 480px; height: 960px;"></div>
    </div>
  </div>
  <div class="box-footer" style=" font-size: 18px;">
  <div id="layoutStatsFooter" class="pull-right">
    <b>Total Shapes: <span id="shapeCount2">0</span> | </b>
    <b> Total Area Used: <span id="areaUsed2">0</span> sq mm |</b>
    <b>Material Efficiency: <span id="efficiency2">0</span>%</b>
  </div>
</div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <style>
    .side-length-input {
  margin-top: 8px;
  padding-left: 15px;
  border-left: 3px solid #ccc;
}

#sideLengthsContainer {
  max-height: 300px;
  overflow-y: auto;
  padding-right: 5px;
}
    /* Mobile optimizations */
@media (max-width: 768px) {
  .canvas-wrapper {
    overflow-x: auto;
    max-width: 100%;
  }
  
  .shape {
    touch-action: none; /* Prevents browser handling of touch events */
  }
  
  .box-tools .btn {
    margin-bottom: 5px;
  }
  
  .mobile-instructions {
    margin-bottom: 15px;
  }
}

/* Touch confirm overlay styling */
.touch-confirm-overlay {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.7);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}
    .canvas-wrapper { 
        position: relative; 
        margin: 20px 0;
        display: inline-block;
    }
    .canvas {
      position: relative;
      border: 3px solid red; /* 3mm border */
      box-sizing: border-box;
      background: white;
      width: 480px;  /* 480px represents 1220mm */
      height: 960px; /* 960px represents 2440mm */
    }
    #canvasContainer {
        white-space: nowrap;
        overflow-x: auto;
        padding: 20px 0;
    }
    .shape {
        position: absolute;
        border: 2px solid #000;
        cursor: move;
        display: flex;
        justify-content: center;  /* Centers horizontally */
        align-items: center;      
        transition: opacity 0.3s;
        font-size: 14px;
        font-weight: bold;
        color: rgba(0,0,0,0.7);
    }
    .grid {
        background-image: 
            linear-gradient(to right, #ccc 1px, transparent 1px),
            linear-gradient(to bottom, #ccc 1px, transparent 1px);
        background-size: 24px 24px; /* 25.4mm grid (about 1 inch) */
    }
    .shape.selected {
      opacity: 0.7 !important;
      border: 2px dashed #000 !important;
      box-shadow: 0 0 5px rgba(0,0,0,0.5);
    }
    .sheet-number {
      position: absolute;
      bottom: 5px;
      right: 5px;
      font-size: 12px;
      color: #666;
      background: rgba(255,255,255,0.8);
      padding: 2px 5px;
      border-radius: 3px;
    }

    /* Context menu */
    .context-menu {
      position: absolute;
      z-index: 1000;
      background: white;
      border: 1px solid #ccc;
      box-shadow: 2px 2px 5px rgba(0,0,0,0.3);
      border-radius: 3px;
      padding: 5px 0;
    }
    .context-menu-item {
      padding: 5px 15px;
      cursor: pointer;
    }
    .context-menu-item:hover {
      background-color: #f0f0f0;
    }
    .measurement-tooltip {
  position: absolute;
  background: rgba(0,0,0,0.7);
  color: white;
  padding: 5px 8px;
  border-radius: 4px;
  font-size: 12px;
  z-index: 1000;
  pointer-events: none;
  white-space: nowrap;
  transition: all 0.2s ease;
  transform: translate(-50%, -100%); /* Center above cursor */
}

.shape.small-shape {
  display: flex;
  justify-content: center;
  align-items: center;
}

.shape.small-shape::after {

  color: #333;
  content: "ℹ"; /* More informative icon */
  font-size: 12px;
  cursor: help;
}
  </style>
<script>
document.getElementById('shapeType').addEventListener('change', function() {
  const customOptions = document.getElementById('customShapeOptions');
  const standardDims = document.getElementById('standardDimensions');
  if (this.value === 'custom') {
    customOptions.style.display = 'block';
    standardDims.style.display = 'none';
  } else {
    customOptions.style.display = 'none';
    standardDims.style.display = 'block';
  }
});
</script>
  <script>
    const SCALE = 0.3934; // 1 mm = 0.3934 pixels (480px / 1220mm)
    let currentCanvasIndex = 1;
    let selectedShape = null;
    let offsetX, offsetY;
    let contextMenu = null;
    
    // Create and add rotation handler
 // Update rotation input handler
document.getElementById('rotation').addEventListener('input', function() {
  const value = parseInt(this.value) || 0;
  // Ensure value is between 0-359
  this.value = Math.max(0, Math.min(359, value));
});
    

    function placeShape(shape) {
      const canvases = document.querySelectorAll('.canvas');
      for (const canvas of canvases) {
        if (tryPlaceInCanvas(shape, canvas)) {
          updateLayoutStats();
          return true;
        }
      }
      return false;
    }

    function tryPlaceInCanvas(shape, canvas) {
      const shapeWidth = parseInt(shape.style.width);
      const shapeHeight = parseInt(shape.style.height);
      const canvasWidth = canvas.offsetWidth;
      const canvasHeight = canvas.offsetHeight;

      const maxAttempts = 100;
      for (let attempt = 0; attempt < maxAttempts; attempt++) {
          const x = Math.floor(Math.random() * (canvasWidth - shapeWidth));
          const y = Math.floor(Math.random() * (canvasHeight - shapeHeight));

          shape.style.left = x + 'px';
          shape.style.top = y + 'px';

          let collision = false;
          for (const existingShape of canvas.children) {
              if (existingShape !== shape && existingShape.classList && existingShape.classList.contains('shape') && checkCollision(shape, existingShape)) {
                  collision = true;
                  break;
              }
          }

          if (!collision) {
              canvas.appendChild(shape);
              return true;
          }
      }

      return false;
    }

    function checkCollision(shape1, shape2) {
      const rect1 = shape1.getBoundingClientRect();
      const rect2 = shape2.getBoundingClientRect();
      
      return !(rect1.right < rect2.left || 
               rect1.left > rect2.right || 
               rect1.bottom < rect2.top || 
               rect1.top > rect2.bottom);
    }
    
    function createNewCanvas() {
      currentCanvasIndex++;
      const canvasWrapper = document.createElement('div');
      canvasWrapper.className = 'canvas-wrapper';
      canvasWrapper.innerHTML = `
          <div class="canvas" data-sheet="${currentCanvasIndex}" 
               style="width: 480px; height: 960px;">
              <div class="sheet-number">Sheet #${currentCanvasIndex}</div>
          </div>
      `;
      document.getElementById('canvasContainer').appendChild(canvasWrapper);
    }

    function canFitInAnyCanvas(width, height) {
      const canvases = document.querySelectorAll('.canvas');
      return Array.from(canvases).some(canvas => {
          return width * SCALE <= canvas.offsetWidth && 
                 height * SCALE <= canvas.offsetHeight;
      });
    }
    
    function addShapes() {
      const type = document.getElementById('shapeType').value;
      const width = parseInt(document.getElementById('shapeWidth').value);
      const height = parseInt(document.getElementById('shapeHeight').value);
      const quantity = parseInt(document.getElementById('quantity').value);
      const color = document.getElementById('shapeColor').value;
      const rotation = parseInt(document.getElementById('rotation').value);

      if (width > 1220 || height > 2440) {
          alert('Dimensions exceed maximum canvas size (1220 mm x 2440 mm)');
          return;
      }

      for (let i = 0; i < quantity; i++) {
          const shape = createShape(type, width, height, color, rotation);
          
          // First try to place in existing canvases
          if (!placeShape(shape)) {
              // Check if it can fit in a new canvas
              if (width <= 1220 && height <= 2440) {
                  createNewCanvas();
                  placeShape(shape);
              } else {
                  alert('Shape is too large for any canvas!');
                  shape.remove();
              }
          }
      }
      updateLayoutStats();
    }

    function createShape(type, width, height, color, rotation = 0) {
      const shape = document.createElement('div');
  shape.className = 'shape';
  shape.style.width = width * SCALE + 'px';
  shape.style.height = height * SCALE + 'px';
  shape.style.backgroundColor = color;
      
      // Store original dimensions as data attributes
      shape.dataset.width = width;
      shape.dataset.height = height;
      shape.dataset.type = type;
      
      if (rotation !== 0) {
        shape.style.transform = `rotate(${rotation}deg)`;
        shape.dataset.rotation = rotation;
      }

      // Add shape-specific styling
      switch (type) {
        case 'circle':
            shape.style.borderRadius = '50%';
            break;
        case 'ellipse':
            shape.style.borderRadius = '50%';
            break;
            case 'custom':
  // Generate polygon with specified number of sides
  const sides = parseInt(document.getElementById('shapeSides').value) || 6;
  shape.style.clipPath = generateRegularPolygon(sides);
  // Store sides in dataset
  shape.dataset.sides = sides;
  
  // Store side lengths in dataset
  const sideLengths = [];
  for (let i = 1; i <= sides; i++) {
    const sideElement = document.getElementById(`sideLength${i}`);
    const sideLength = sideElement ? parseInt(sideElement.value) : 50;
    sideLengths.push(sideLength);
  }
  shape.dataset.sideLengths = JSON.stringify(sideLengths);
  break;
        case 'triangle':
            shape.style.clipPath = 'polygon(50% 0%, 0% 100%, 100% 100%)';
            break;
        case 'invertedTriangle':
            shape.style.clipPath = 'polygon(50% 100%, 0% 0%, 100% 0%)';
            break;
        case 'rightTriangle':
            shape.style.clipPath = 'polygon(0% 0%, 0% 100%, 100% 100%)';
            break;
        case 'pentagon':
            shape.style.clipPath = 'polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%)';
            break;
        case 'hexagon':
            shape.style.clipPath = 'polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%)';
            break;
        case 'octagon':
            shape.style.clipPath = 'polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%)';
            break;
        case 'trapezoid':
            shape.style.clipPath = 'polygon(20% 0%, 80% 0%, 100% 100%, 0% 100%)';
            break;
        case 'parallelogram':
            shape.style.clipPath = 'polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%)';
            break;
        case 'rhombus':
            shape.style.clipPath = 'polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)';
            break;
        case 'star':
            shape.style.clipPath = 'polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%)';
            break;
        case 'arrow':
            shape.style.clipPath = 'polygon(0% 30%, 70% 30%, 70% 0%, 100% 50%, 70% 100%, 70% 70%, 0% 70%)';
            break;
        case 'cross':
            shape.style.clipPath = 'polygon(35% 0%, 65% 0%, 65% 35%, 100% 35%, 100% 65%, 65% 65%, 65% 100%, 35% 100%, 35% 65%, 0% 65%, 0% 35%, 35% 35%)';
            break;
            
      }

  // Check if shape is too small for a measurement tag
  const pixelWidth = width * SCALE;
  const pixelHeight = height * SCALE;
  const isSmallShape = pixelWidth < 80 || pixelHeight < 30;
  
  if (isSmallShape) {
  shape.classList.add('small-shape', 'hover-enabled');
  
  // Custom shape measurement text
  if (type === 'custom') {
    const sideLengths = JSON.parse(shape.dataset.sideLengths || '[]');
    const labels = sideLengths.map((len, index) => 
      `${String.fromCharCode(65 + index)}: ${len}mm`
    );
    shape.dataset.measurementText = labels.join(', ');
  } else {
    shape.dataset.measurementText = `${width}mm x ${height}mm`;
  }
  
  shape.addEventListener('mouseover', showMeasurementTooltip);
  shape.addEventListener('mouseout', hideMeasurementTooltip);
} else {
  // For larger shapes
  if (type === 'custom') {
    const sideLengths = JSON.parse(shape.dataset.sideLengths || '[]');
    const labels = sideLengths.map((len, index) => 
      `${String.fromCharCode(65 + index)}: ${len}mm`
    );
    shape.innerHTML = labels.join('<br>');
  } else {
    shape.innerHTML = `${width}mm x ${height}mm`;
  }
}

shape.addEventListener('mousedown', startDrag);
shape.addEventListener('contextmenu', showContextMenu);

// Add touch event listeners
shape.addEventListener('touchstart', startTouchDrag);
shape.addEventListener('touchmove', touchDrag);
shape.addEventListener('touchend', stopTouchDrag);

return shape;
}
    
    function showContextMenu(e) {
      e.preventDefault();
      e.stopPropagation(); 
      
      // Remove any existing context menu
      if (contextMenu) {
        contextMenu.remove();
      }
      
      // Set the clicked shape as selected
      if (selectedShape) {
        selectedShape.classList.remove('selected');
      }
      selectedShape = e.target;
      selectedShape.classList.add('selected');
        // Update rotation controls
  updateRotationControls();
      // Create context menu
      contextMenu = document.createElement('div');
      contextMenu.className = 'context-menu';
      contextMenu.innerHTML = `
        <div class="context-menu-item" onclick="duplicateSelectedShape()">Duplicate</div>
        <div class="context-menu-item" onclick="rotateShape(45)">Rotate 45°</div>
        <div class="context-menu-item" onclick="rotateShape(-45)">Rotate -45°</div>
        <div class="context-menu-item" onclick="deleteSelectedShape()">Delete</div>
      `;
      
      // Position context menu
      contextMenu.style.left = e.pageX + 'px';
      contextMenu.style.top = e.pageY + 'px';
      
      // Add to document
      document.body.appendChild(contextMenu);
      
      // Close on outside click
      document.addEventListener('click', closeContextMenu);
    }
    
    function closeContextMenu() {
      if (contextMenu) {
        contextMenu.remove();
        contextMenu = null;
      }
      document.removeEventListener('click', closeContextMenu);
    }
    
    function duplicateSelectedShape() {
      if (!selectedShape) return;
      
      const type = selectedShape.dataset.type;
      const width = parseInt(selectedShape.dataset.width);
      const height = parseInt(selectedShape.dataset.height);
      const color = selectedShape.style.backgroundColor;
      const rotation = selectedShape.dataset.rotation || 0;
      let newShape;
      if (type === 'custom') {
  // For custom shapes, create the shape directly
  newShape = document.createElement('div');
  newShape.className = 'shape';
  newShape.style.width = width * SCALE + 'px';
  newShape.style.height = height * SCALE + 'px';
  newShape.style.backgroundColor = color;
  newShape.dataset.width = width;
  newShape.dataset.height = height;
  newShape.dataset.type = type;
  
  const sides = parseInt(selectedShape.dataset.sides) || 6;
  newShape.dataset.sides = sides;
  
  // Copy side lengths if available
  if (selectedShape.dataset.sideLengths) {
    newShape.dataset.sideLengths = selectedShape.dataset.sideLengths;
  }
  
  // Generate polygon using the same parameters
  let clipPath;
  if (selectedShape.dataset.sideLengths) {
    // Re-create polygon from stored side lengths
    const sideLengths = JSON.parse(selectedShape.dataset.sideLengths);
    clipPath = generatePolygonFromLengths(sides, sideLengths);
  } else {
    clipPath = generateRegularPolygon(sides);
  }
  newShape.style.clipPath = clipPath;
  
  newShape.innerHTML = `${width}mm x ${height}mm`;
  newShape.addEventListener('mousedown', startDrag);
  newShape.addEventListener('contextmenu', showContextMenu);
  newShape.addEventListener('touchstart', startTouchDrag);
  newShape.addEventListener('touchmove', touchDrag);
  newShape.addEventListener('touchend', stopTouchDrag);
} else {
    // For standard shapes, use the createShape function
    newShape = createShape(type, width, height, color, rotation);

// Check if original was a small shape and copy the measurement text
if (selectedShape.classList.contains('small-shape')) {
  newShape.classList.add('small-shape');
  newShape.dataset.measurementText = selectedShape.dataset.measurementText;
  newShape.innerHTML = ''; // Clear inner HTML for small shapes
  
  // Add event listeners for tooltip
  newShape.addEventListener('mouseover', showMeasurementTooltip);
  newShape.addEventListener('mouseout', hideMeasurementTooltip);
}
  }
  
  // Get current canvas
  const canvas = selectedShape.parentElement;
  
  // Position slightly offset from original
  const left = parseInt(selectedShape.style.left) + 20;
  const top = parseInt(selectedShape.style.top) + 20;
  
  newShape.style.left = left + 'px';
  newShape.style.top = top + 'px';
  
  if (rotation !== 0) {
    newShape.style.transform = `rotate(${rotation}deg)`;
    newShape.dataset.rotation = rotation;
  }
  
  canvas.appendChild(newShape);
  updateLayoutStats();
}
    
    function rotateShape(degrees) {
      if (!selectedShape) return;
      
      const currentRotation = selectedShape.dataset.rotation ? parseInt(selectedShape.dataset.rotation) : 0;
      const newRotation = (currentRotation + degrees) % 360;
      
      selectedShape.style.transform = `rotate(${newRotation}deg)`;
      selectedShape.dataset.rotation = newRotation;
    }
    
    function deleteSelectedShape() {
      if (!selectedShape) return;
      
      selectedShape.remove();
      selectedShape = null;
      updateLayoutStats();
        
  // Hide rotation controls when shape is deleted
  updateRotationControls();
    }


    
    function updateLayoutStats() {
  const canvases = document.querySelectorAll('.canvas');
  let totalShapes = 0;
  let totalArea = 0;
  const totalCanvasArea = 1220 * 2440 * canvases.length;

  canvases.forEach(canvas => {
    const shapes = canvas.querySelectorAll('.shape');
    totalShapes += shapes.length;
    
    shapes.forEach(shape => {
      // Use original mm dimensions from dataset for accurate area calculation
      const width = parseInt(shape.dataset.width);
      const height = parseInt(shape.dataset.height);
      totalArea += width * height;
    });
  });

  // Update both header and footer stats
  document.getElementById('shapeCount').textContent = totalShapes;
  document.getElementById('areaUsed').textContent = totalArea.toFixed(1);
  
  // Calculate efficiency
  const efficiency = totalCanvasArea > 0 ? (totalArea / totalCanvasArea * 100).toFixed(1) : 0;
  document.getElementById('efficiency').textContent = efficiency;
  
  // Update footer stats
  if (document.getElementById('shapeCount2')) {
    document.getElementById('shapeCount2').textContent = totalShapes;
    document.getElementById('areaUsed2').textContent = totalArea.toFixed(1);
    document.getElementById('efficiency2').textContent = efficiency;
  }
}
    
    function startDrag(e) {
      if (e.button !== 0) return; // Only respond to left-clicks
      e.stopPropagation();
      
      // Deselect previously selected shape
      if (selectedShape) {
        selectedShape.classList.remove('selected');
      }

      selectedShape = e.target;
      selectedShape.classList.add('selected');
       // Show rotation controls for the selected shape
  updateRotationControls();

      offsetX = e.offsetX;
      offsetY = e.offsetY;

      document.addEventListener('mousemove', drag);
      document.addEventListener('mouseup', stopDrag);
    }

    function drag(e) {
      if (!selectedShape) return;

      const canvases = document.querySelectorAll('.canvas');
      let targetCanvas = null;

      // Check which canvas the cursor is over
      canvases.forEach(canvas => {
          const rect = canvas.getBoundingClientRect();
          if (
              e.clientX >= rect.left && e.clientX <= rect.right &&
              e.clientY >= rect.top && e.clientY <= rect.bottom
          ) {
              targetCanvas = canvas;
          }
      });

      if (targetCanvas) {
          targetCanvas.appendChild(selectedShape); // Re-parent the shape to the new canvas
          const canvasRect = targetCanvas.getBoundingClientRect();

          const borderOffset = 3 * SCALE; // 3mm border to pixels
          const x = e.clientX - canvasRect.left - offsetX;
          const y = e.clientY - canvasRect.top - offsetY;

          // Ensure shape stays within bounds of the target canvas
          const maxX = targetCanvas.offsetWidth - selectedShape.offsetWidth - borderOffset;
          const maxY = targetCanvas.offsetHeight - selectedShape.offsetHeight - borderOffset;

          selectedShape.style.left = Math.max(borderOffset, Math.min(x, maxX)) + 'px';
          selectedShape.style.top = Math.max(borderOffset, Math.min(y, maxY)) + 'px';
      }
    }

    function removeLastCanvas() {
      const canvasWrappers = document.querySelectorAll('.canvas-wrapper');

      // Ensure at least one canvas remains
      if (canvasWrappers.length > 1) {
          const lastWrapper = canvasWrappers[canvasWrappers.length - 1];
          lastWrapper.remove();
          currentCanvasIndex--;

          // Update stats if shapes were on the removed canvas
          updateLayoutStats();
      } else {
          alert("At least one canvas must remain.");
      }
    }

    function stopDrag() {
  document.removeEventListener('mousemove', drag);
  document.removeEventListener('mouseup', stopDrag);
  
  // Hide tooltip when drag ends
  hideMeasurementTooltip();

  // Refresh stats after move
  updateLayoutStats();
}

    function clearCanvases() {
      document.querySelectorAll('.canvas-wrapper').forEach(wrapper => {
          if (wrapper.dataset.sheet !== '1') wrapper.remove();
      });
      const firstCanvas = document.querySelector('[data-sheet="1"]');
      // Clear all shapes but keep the sheet number
      const sheetNumber = firstCanvas.querySelector('.sheet-number');
      firstCanvas.innerHTML = '';
      if (sheetNumber) {
        firstCanvas.appendChild(sheetNumber);
      }
      currentCanvasIndex = 1;
      selectedShape = null;
      updateLayoutStats();
    }
    
    function toggleGrid() {
      document.querySelectorAll('.canvas').forEach(canvas => {
        canvas.classList.toggle('grid');
      });
    }
    
    document.addEventListener('keydown', function(e) {
      // Backspace or Delete key
      if ((e.keyCode === 8 || e.keyCode === 46) && selectedShape) {
          // Only delete if not focused on input fields
          if (document.activeElement.tagName !== 'INPUT') {
              selectedShape.remove();
              selectedShape = null;
              updateLayoutStats();
          }
      }
      
      // Keyboard shortcuts for rotation
      if (selectedShape) {
        if (e.keyCode === 82) { // R key
          rotateShape(45); // Rotate 45 degrees
        }
      }
    });
// Detect if device supports touch
function isTouchDevice() {
  return ('ontouchstart' in window) || 
         (navigator.maxTouchPoints > 0) || 
         (navigator.msMaxTouchPoints > 0);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  // Toggle grid on by default
  toggleGrid();
  
  // Show mobile instructions if on a touch device
  if (isTouchDevice()) {
    const mobileInstructions = document.querySelector('.mobile-instructions');
    if (mobileInstructions) {
      mobileInstructions.style.display = 'block';
    }
  }
});
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      // Toggle grid on by default
      toggleGrid();
    });
    // Touch event handlers
function startTouchDrag(e) {
  e.preventDefault();
  if (e.target.closest('.main-sidebar')) return; // Add this line
  // Deselect previously selected shape
  if (selectedShape) {
    selectedShape.classList.remove('selected');
  }

  selectedShape = e.target;
  selectedShape.classList.add('selected');
    // Show rotation controls for the selected shape
    updateRotationControls();
  
  const touch = e.touches[0];
  const rect = selectedShape.getBoundingClientRect();
  
  // Calculate offset within the element
  offsetX = touch.clientX - rect.left;
  offsetY = touch.clientY - rect.top;
  
  // Start long press timer for deletion
  longPressTimer = setTimeout(() => {
    vibrate();
    showTouchDeleteConfirm(selectedShape);
  }, 800); // 800ms long press
}

function touchDrag(e) {
  if (!selectedShape) return;
  e.preventDefault();
  
  // Clear long press timer since we're moving
  clearTimeout(longPressTimer);
  
  const touch = e.touches[0];
  const canvases = document.querySelectorAll('.canvas');
  let targetCanvas = null;

  // Check which canvas the touch is over
  canvases.forEach(canvas => {
    const rect = canvas.getBoundingClientRect();
    if (
      touch.clientX >= rect.left && touch.clientX <= rect.right &&
      touch.clientY >= rect.top && touch.clientY <= rect.bottom
    ) {
      targetCanvas = canvas;
    }
  });

  if (targetCanvas) {
    targetCanvas.appendChild(selectedShape); // Re-parent the shape to the new canvas
    const canvasRect = targetCanvas.getBoundingClientRect();

    const borderOffset = 3 * SCALE; // 3mm border to pixels
    const x = touch.clientX - canvasRect.left - offsetX;
    const y = touch.clientY - canvasRect.top - offsetY;

    // Ensure shape stays within bounds of the target canvas
    const maxX = targetCanvas.offsetWidth - selectedShape.offsetWidth - borderOffset;
    const maxY = targetCanvas.offsetHeight - selectedShape.offsetHeight - borderOffset;

    selectedShape.style.left = Math.max(borderOffset, Math.min(x, maxX)) + 'px';
    selectedShape.style.top = Math.max(borderOffset, Math.min(y, maxY)) + 'px';
  }
}

function stopTouchDrag() {
  // Clear long press timer
  clearTimeout(longPressTimer);
  
  // Update stats after move
  updateLayoutStats();
}

function showTouchDeleteConfirm(shape) {
  // Create a simple confirmation overlay
  const confirmOverlay = document.createElement('div');
  confirmOverlay.className = 'touch-confirm-overlay';
  confirmOverlay.style.position = 'fixed';
  confirmOverlay.style.left = '0';
  confirmOverlay.style.top = '0';
  confirmOverlay.style.width = '100%';
  confirmOverlay.style.height = '100%';
  confirmOverlay.style.backgroundColor = 'rgba(0,0,0,0.7)';
  confirmOverlay.style.zIndex = '9999';
  confirmOverlay.style.display = 'flex';
  confirmOverlay.style.justifyContent = 'center';

  confirmOverlay.style.flexDirection = 'column';
  
  const message = document.createElement('div');
  message.style.color = 'white';
  message.style.marginBottom = '20px';
  message.style.fontSize = '18px';
  message.style.textAlign = 'center'; // Center the text
  message.textContent = 'Delete this shape?';
  
  const buttonContainer = document.createElement('div');
  buttonContainer.style.display = 'flex';
  buttonContainer.style.gap = '20px';
  buttonContainer.style.justifyContent = 'center'; // Center the buttons
  
  const cancelBtn = document.createElement('button');
  cancelBtn.className = 'btn btn-default';
  cancelBtn.textContent = 'Cancel';
  cancelBtn.onclick = () => confirmOverlay.remove();
  
  const deleteBtn = document.createElement('button');
  deleteBtn.className = 'btn btn-danger';
  deleteBtn.textContent = 'Delete';
  deleteBtn.onclick = () => {
    shape.remove();
    selectedShape = null;
    updateLayoutStats();
    confirmOverlay.remove();
  };
  
  buttonContainer.appendChild(cancelBtn);
  buttonContainer.appendChild(deleteBtn);
  confirmOverlay.appendChild(message);
  confirmOverlay.appendChild(buttonContainer);
  document.body.appendChild(confirmOverlay);
}
// Optional: add vibration for tactile feedback (if supported)
function vibrate() {
  if (navigator.vibrate) {
    navigator.vibrate(50);
  }
}

// Variable for long press detection
let longPressTimer;
function generateRegularPolygon(sides) {
  if (sides < 3) sides = 3; // Minimum 3 sides (triangle)
  if (sides > 20) sides = 20; // Maximum 20 sides
  
  let points = [];
  const sideLengths = [];
  
  // Collect all side lengths
  for (let i = 1; i <= sides; i++) {
    const sideElement = document.getElementById(`sideLength${i}`);
    const sideLength = sideElement ? parseInt(sideElement.value) : 50;
    sideLengths.push(sideLength);
  }
  
  // If using custom side lengths
  if (sideLengths.length > 0) {
    // Calculate points for an irregular polygon with custom side lengths
    // For simplicity, we'll still create a regular polygon shape
    // but scale each vertex based on the corresponding side length
    const angleStep = 360 / sides;
    let maxSideLength = Math.max(...sideLengths);
    
    for (let i = 0; i < sides; i++) {
      const scaleFactor = sideLengths[i] / maxSideLength;
      const radius = 50 * scaleFactor; // Scale radius by the side length ratio
      const angle = (i * angleStep - 90) * Math.PI / 180;
      const x = 50 + radius * Math.cos(angle);
      const y = 50 + radius * Math.sin(angle);
      points.push(`${x}% ${y}%`);
    }
  } else {
    // Original regular polygon code
    const angleStep = 360 / sides;
    const radius = 50;
    
    for (let i = 0; i < sides; i++) {
      const angle = (i * angleStep - 90) * Math.PI / 180;
      const x = 50 + radius * Math.cos(angle);
      const y = 50 + radius * Math.sin(angle);
      points.push(`${x}% ${y}%`);
    }
  }
  
  return `polygon(${points.join(', ')})`;
}
// Function to apply rotation to selected shape
function applyRotationToSelected() {
  if (!selectedShape) {
    alert('Please select a shape first');
    return;
  }
  
  const rotation = parseInt(document.getElementById('selectedShapeRotation').value) || 0;
  // Ensure value is between 0-359
  const normalizedRotation = Math.max(0, Math.min(359, rotation));
  
  selectedShape.style.transform = `rotate(${normalizedRotation}deg)`;
  selectedShape.dataset.rotation = normalizedRotation;
  
  // Hide rotation controls after applying
  //document.getElementById('rotationControls').style.display = 'none';
}

// Show rotation controls and update value when shape is selected
function updateRotationControls() {
  const rotationControls = document.getElementById('rotationControls');
  
  if (selectedShape) {
    rotationControls.style.display = 'block';
    const currentRotation = selectedShape.dataset.rotation ? parseInt(selectedShape.dataset.rotation) : 0;
    document.getElementById('selectedShapeRotation').value = currentRotation;
  } else {
    rotationControls.style.display = 'none';
  }
}
// Function to generate side length input fields based on number of sides
function generateSideLengthInputs() {
  const sides = parseInt(document.getElementById('shapeSides').value) || 3;
  const container = document.getElementById('sideLengthsContainer');
  
  // Clear previous inputs
  container.innerHTML = '';
  
  // Generate a field for each side
  for (let i = 1; i <= sides; i++) {
    // Create label and input for this side
    const sideInput = document.createElement('div');
    sideInput.className = 'form-group side-length-input';
    sideInput.innerHTML = `
      <label>Length of side ${String.fromCharCode(64 + i)} (mm):</label>
      <input type="number" class="form-control" id="sideLength${i}" min="10" value="50">
    `;
    container.appendChild(sideInput);
  }
}
document.addEventListener('DOMContentLoaded', function() {
  // Show/hide custom shape options based on selection
  document.getElementById('shapeType').addEventListener('change', function() {
    const customOptions = document.getElementById('customShapeOptions');
    if (this.value === 'custom') {
      customOptions.style.display = 'block';
      generateSideLengthInputs(); // Generate inputs when custom is selected
    } else {
      customOptions.style.display = 'none';
    }
  });
  
  // Generate inputs when number of sides changes
  document.getElementById('shapeSides').addEventListener('input', function() {
    generateSideLengthInputs();
  });
});
function generatePolygonFromLengths(sides, sideLengths) {
  let points = [];
  const angleStep = 360 / sides;
  let maxSideLength = Math.max(...sideLengths);
  
  for (let i = 0; i < sides; i++) {
    const scaleFactor = sideLengths[i] / maxSideLength;
    const radius = 50 * scaleFactor;
    const angle = (i * angleStep - 90) * Math.PI / 180;
    const x = 50 + radius * Math.cos(angle);
    const y = 50 + radius * Math.sin(angle);
    points.push(`${x}% ${y}%`);
  }
  
  return `polygon(${points.join(', ')})`;
}
let measurementTooltip = null;
function showMeasurementTooltip(e) {
  if (!measurementTooltip) {
    measurementTooltip = document.createElement('div');
    measurementTooltip.className = 'measurement-tooltip';
    document.body.appendChild(measurementTooltip);
  }

  const text = e.target.dataset.measurementText;
  measurementTooltip.textContent = text;
  measurementTooltip.style.display = 'block';

  // Add mouse move listener for current shape
  const shape = e.target;
  const moveHandler = (e) => {
    const mouseX = e.clientX || e.touches[0].clientX;
    const mouseY = e.clientY || e.touches[0].clientY;
    
    measurementTooltip.style.left = `${mouseX + window.scrollX}px`;
    measurementTooltip.style.top = `${mouseY + window.scrollY - 15}px`;
  };

  shape.addEventListener('mousemove', moveHandler);
  shape.addEventListener('touchmove', moveHandler);

  // Store reference for cleanup
  shape._moveHandler = moveHandler;
}

function hideMeasurementTooltip(e) {
  if (measurementTooltip) {
    measurementTooltip.style.display = 'none';
    const shape = e.target;
    if (shape._moveHandler) {
      shape.removeEventListener('mousemove', shape._moveHandler);
      shape.removeEventListener('touchmove', shape._moveHandler);
    }
  }
}
$(document).ready(function() {
  // Reinitialize treeview functionality
  $('.sidebar-menu').tree('refresh');
  
  // Prevent drag events from interfering with treeview clicks
  $('.sidebar-menu').on('mousedown touchstart', function(e) {
    e.stopPropagation();
  });
});
  </script>

  <?php include 'includes/footer.php'; ?>
</div>
</body>
</html>