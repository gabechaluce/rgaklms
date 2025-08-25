<!-- jQuery 3 -->
<script src="../bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Moment JS -->
<script src="../bower_components/moment/moment.js"></script>
<!-- DataTables -->
<script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- ChartJS -->
<script src="../bower_components/chart.js/Chart.js"></script>
<!-- daterangepicker -->
<script src="../bower_components/moment/min/moment.min.js"></script>
<script src="../bower_components/bootstrap-daterangepicker/daterangepicker.js"><x/script>
<!-- datepicker -->
<script src="../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- bootstrap time picker -->
<script src="../plugins/timepicker/bootstrap-timepicker.min.js"></script>
<!-- Slimscroll -->
<script src="../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- Active Script -->
<script>
$(function(){
	/** add active class and stay opened when selected */
	var url = window.location;

	// for sidebar menu entirely but not cover treeview
	$('ul.sidebar-menu a').filter(function() {
	    return this.href == url;
	}).parent().addClass('active');

	// for treeview
	$('ul.treeview-menu a').filter(function() {
	    return this.href == url;
	}).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');

});
</script>
<!-- Data Table Initialize -->
<script>
  $(function () {
    $('#example1').DataTable({
      responsive: true
    })
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })
</script>
<!-- Date and Timepicker -->
<script>
  //Date picker
  $('#datepicker_add').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  })
  $('#datepicker_edit').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  }) 
</script>
<script>
        const canvasMargin = 3; // 3px = 3mm in your scaled layout

        const scaleFactor = 0.25;
        const canvasWidth = 960;
        const canvasHeight = 480;
        const gap = 0;
        let canvases = [{ element: document.getElementById("canvas"), occupiedSpaces: [] }];
        let draggedElement = null;
        let offsetX = 0;
        let offsetY = 0;
        let originalX = 0;
        let originalY = 0;
        let currentCanvasIndex = 0;
        let isTriangle = false;
        let gridVisible = false;

        // Initialize with default values
        document.addEventListener('DOMContentLoaded', function() {
            // Set default values
            document.getElementById("shapeWidth").value = 12;
            document.getElementById("shapeHeight").value = 12;
            
            // Add event listeners for shape type change
            document.getElementById("shapeType").addEventListener('change', function() {
                if (this.value === "square") {
                    const widthValue = document.getElementById("shapeWidth").value;
                    document.getElementById("shapeHeight").value = widthValue;
                    document.getElementById("shapeHeight").disabled = true;
                } else {
                    document.getElementById("shapeHeight").disabled = false;
                }
            });
            
            // Add event listeners for width change when square is selected
            document.getElementById("shapeWidth").addEventListener('input', function() {
                if (document.getElementById("shapeType").value === "square") {
                    document.getElementById("shapeHeight").value = this.value;
                }
            });
        });

        function addShapes() {
            const shapeType = document.getElementById("shapeType").value;
            const width = parseInt(document.getElementById("shapeWidth").value) * scaleFactor;
            const height = parseInt(document.getElementById("shapeHeight").value) * scaleFactor;
            const quantity = parseInt(document.getElementById("shapeQuantity").value);
            
            if (width > 0 && height > 0 && quantity > 0) {
                let placed = 0;
                while (placed < quantity) {
                    let canvasObj = canvases[canvases.length - 1];
                    let fit = false;
                    for (let y = canvasMargin; y < canvasHeight - height - canvasMargin && !fit; y += height) {
    for (let x = canvasMargin; x < canvasWidth - width - canvasMargin && !fit; x += width) {

                            if (shapeType === "triangle") {
                                if ((y / height) % 2 === 1) {
                                    x += width / 2; 
                                    if (x + width / 2 > canvasWidth) break; 
                                }
                            }
                            if (
    !isOccupied(canvasObj.occupiedSpaces, x, y, width, height) &&
    (x + width <= canvasWidth - canvasMargin) &&
    (y + height <= canvasHeight - canvasMargin) &&
    (x >= canvasMargin && y >= canvasMargin)
) {

                                placeShape(canvasObj.element, x, y, width, height, shapeType, (y / height) % 2 === 1);
                                canvasObj.occupiedSpaces.push({ x, y, width, height });
                                placed++;
                                fit = true;
                            }
                        }
                    }
                    if (!fit) {
                        addNewCanvas();
                        currentCanvasIndex = canvases.length - 1;
                        showToast(`Added Sheet ${canvases.length} - Not enough space on previous sheet`);
                    }
                }
                showToast(`Added ${quantity} ${shapeType}(s)`);
            }
        }

        function isOccupied(occupiedSpaces, x, y, width, height) {
            return occupiedSpaces.some(space => 
                x < space.x + space.width &&
                x + width > space.x &&
                y < space.y + space.height &&
                y + height > space.y
            );
        }

        function placeShape(canvas, x, y, width, height, shapeType, inverted = false) {
            const shape = document.createElement("div");
            shape.classList.add("shape");
            shape.setAttribute("data-size", `${Math.round(width/scaleFactor)}in x ${Math.round(height/scaleFactor)}in`);
            shape.setAttribute("data-shape-type", shapeType);
            
            if (shapeType === "circle") {
                shape.classList.add("circle");
                shape.style.width = `${width}px`;
                shape.style.height = `${width}px`;
            } else if (shapeType === "triangle") {
                shape.classList.add("triangle");
                shape.style.width = "0";
                shape.style.height = "0";
                shape.style.borderLeft = `${width / 2}px solid transparent`;
                shape.style.borderRight = `${width / 2}px solid transparent`;
                shape.style.borderBottom = `${height}px solid ${getRandomColor()}`;
                shape.style.background = "none";
                if (inverted) {
                    shape.style.transform = "rotate(180deg)";
                }
            } else {
                shape.style.width = `${width}px`;
                shape.style.height = `${height}px`;
                shape.style.backgroundColor = getRandomColor();
            }
            
            shape.style.left = `${x}px`;
            shape.style.top = `${y}px`;
            
            // Add drag events
            shape.addEventListener('mousedown', dragStart);
            
            canvas.appendChild(shape);
        }

        function getRandomColor() {
            const colors = [
                '#4361ee', '#3a0ca3', '#7209b7', '#f72585', 
                '#4cc9f0', '#4895ef', '#560bad', '#b5179e',
                '#3a86ff', '#ff006e', '#8338ec', '#fb5607'
            ];
            return colors[Math.floor(Math.random() * colors.length)];
        }

        function addNewCanvas() {
            const newCanvas = document.createElement("div");
            newCanvas.classList.add("canvas-container");
            
            const canvasTitle = document.createElement("div");
            canvasTitle.classList.add("canvas-title");
            canvasTitle.textContent = `Sheet ${canvases.length + 1} (4ft x 8ft)`;
            newCanvas.appendChild(canvasTitle);
            
            document.getElementById("canvasContainer").appendChild(newCanvas);
            canvases.push({ element: newCanvas, occupiedSpaces: [] });
            
            if (gridVisible) {
                drawGrid(newCanvas);
            }
        }
        
        // Drag functionality
        function dragStart(e) {
            draggedElement = this;
            
            // Store original position
            originalX = parseInt(draggedElement.style.left);
            originalY = parseInt(draggedElement.style.top);
            
            // Check if it's a triangle
            isTriangle = draggedElement.getAttribute("data-shape-type") === "triangle";
            
            // Calculate offset from mouse position to element corner
            const rect = draggedElement.getBoundingClientRect();
            offsetX = e.clientX - rect.left;
            offsetY = e.clientY - rect.top;
            
            // Add dragging appearance
            draggedElement.classList.add('dragging');
            
            // Find current canvas
            canvases.forEach((canvas, index) => {
                if (canvas.element.contains(draggedElement)) {
                    currentCanvasIndex = index;
                    
                    // Remove this space from occupied spaces
                    const width = isTriangle ? 
                      parseInt(draggedElement.style.borderLeft) * 2 : 
                      (parseInt(draggedElement.style.width) || draggedElement.offsetWidth);
                      
                    const height = isTriangle ? 
                      parseInt(draggedElement.style.borderBottom) : 
                      (parseInt(draggedElement.style.height) || draggedElement.offsetHeight);
                    
                    canvas.occupiedSpaces = canvas.occupiedSpaces.filter(space => 
                        !(space.x === originalX && space.y === originalY)
                    );
                }
            });
            
            // Add event listeners
            document.addEventListener('mousemove', dragMove);
            document.addEventListener('mouseup', dragEnd);
            
            e.preventDefault(); // Prevent default behavior
        }
        
        function dragMove(e) {
            if (!draggedElement) return;
            
            // Calculate new position
            const canvas = canvases[currentCanvasIndex].element;
            const canvasRect = canvas.getBoundingClientRect();
            
            let newX = e.clientX - canvasRect.left - offsetX;
            let newY = e.clientY - canvasRect.top - offsetY;
            
            // Get dimensions based on shape type
            let width, height;
            if (isTriangle) {
                width = parseInt(draggedElement.style.borderLeft) * 2;
                height = parseInt(draggedElement.style.borderBottom);
            } else {
                width = parseInt(draggedElement.style.width) || draggedElement.offsetWidth;
                height = parseInt(draggedElement.style.height) || draggedElement.offsetHeight;
            }
            
            // Constrain to canvas boundaries
          // Constrain to canvas boundaries WITH margin
newX = Math.max(canvasMargin, Math.min(canvasWidth - width - canvasMargin, newX));
newY = Math.max(canvasMargin, Math.min(canvasHeight - height - canvasMargin, newY));

            
            // Update position
            draggedElement.style.left = `${newX}px`;
            draggedElement.style.top = `${newY}px`;
            
            // Check for overlap and update appearance
            const canvasObj = canvases[currentCanvasIndex];
            const hasOverlap = isOccupied(canvasObj.occupiedSpaces, newX, newY, width, height);
            
            if (hasOverlap) {
                draggedElement.classList.add('overlap');
            } else {
                draggedElement.classList.remove('overlap');
            }
            if (
    originalX < canvasMargin || originalY < canvasMargin ||
    originalX + width > canvasWidth - canvasMargin ||
    originalY + height > canvasHeight - canvasMargin
) {
    showToast("Original position was outside margin. Please drag again.");
    draggedElement.remove();
    return;
}

        }
        
        
        function dragEnd(e) {
            if (!draggedElement) return;
            
            // Remove dragging appearance
            draggedElement.classList.remove('dragging');
            
            // Get final position
            const finalX = parseInt(draggedElement.style.left);
            const finalY = parseInt(draggedElement.style.top);
            
            // Get dimensions based on shape type
            let width, height;
            if (isTriangle) {
                width = parseInt(draggedElement.style.borderLeft) * 2;
                height = parseInt(draggedElement.style.borderBottom);
            } else {
                width = parseInt(draggedElement.style.width) || draggedElement.offsetWidth;
                height = parseInt(draggedElement.style.height) || draggedElement.offsetHeight;
            }
            
            // Check if new position overlaps with existing shapes
            const canvasObj = canvases[currentCanvasIndex];
            
            if (isOccupied(canvasObj.occupiedSpaces, finalX, finalY, width, height)) {
                // Collision detected, move back to original position
                draggedElement.style.left = `${originalX}px`;
                draggedElement.style.top = `${originalY}px`;
                draggedElement.classList.remove('overlap');
                
                // Add back to occupied spaces
                canvasObj.occupiedSpaces.push({
                    x: originalX,
                    y: originalY,
                    width: width,
                    height: height
                });
                
                showToast("Shape overlap detected - moved back to original position");
            } else {
                // No collision, update occupied spaces with new position
                canvasObj.occupiedSpaces.push({
                    x: finalX,
                    y: finalY,
                    width: width,
                    height: height
                });
            }
            
            // Clean up
            document.removeEventListener('mousemove', dragMove);
            document.removeEventListener('mouseup', dragEnd);
            draggedElement = null;
        }
        
        // Grid functionality
        function toggleGrid() {
            gridVisible = !gridVisible;
            
            if (gridVisible) {
                canvases.forEach(canvas => {
                    drawGrid(canvas.element);
                });
                showToast("Grid enabled");
            } else {
                canvases.forEach(canvas => {
                    const gridLines = canvas.element.querySelectorAll('.grid-line, .grid-line-label');
                    gridLines.forEach(line => line.remove());
                });
                showToast("Grid disabled");
            }
        }
        
        function drawGrid(canvas) {
    // Clear any existing grid first
    const existingGrid = canvas.querySelectorAll('.grid-line, .grid-line-label');
    existingGrid.forEach(element => element.remove());
    
    // Draw grid lines every 6 inches (scaled)
    const gridSize = 6 * scaleFactor;
    
    // Draw horizontal lines
    for (let y = 0; y <= canvasHeight; y += gridSize) {
        const line = document.createElement("div");
        line.classList.add("grid-line");
        line.style.left = "0";
        line.style.top = `${y}px`;
        line.style.width = "100%";
        line.style.height = "1px";
        canvas.appendChild(line);
        
        // Add inch marker every foot (12 inches)
        if (y % (12 * scaleFactor) === 0) {
            const label = document.createElement("div");
            label.classList.add("grid-line-label");
            label.style.left = "5px";
            label.style.top = `${y + 2}px`;
            label.textContent = `${Math.round(y / scaleFactor)}″`;
            canvas.appendChild(label);
        }
    }
    
    // Draw vertical lines
    for (let x = 0; x <= canvasWidth; x += gridSize) {
        const line = document.createElement("div");
        line.classList.add("grid-line");
        line.style.left = `${x}px`;
        line.style.top = "0";
        line.style.width = "1px";
        line.style.height = "100%";
        canvas.appendChild(line);
        
        // Add inch marker every foot (12 inches)
        if (x % (12 * scaleFactor) === 0) {
            const label = document.createElement("div");
            label.classList.add("grid-line-label");
            label.style.left = `${x + 2}px`;
            label.style.top = "5px";
            label.textContent = `${Math.round(x / scaleFactor)}″`;
            canvas.appendChild(label);
        }
    }
}
        function clearShapes() {
            if (confirm("Are you sure you want to clear all shapes?")) {
                canvases.forEach(canvas => {
                    const shapes = canvas.element.querySelectorAll('.shape');
                    shapes.forEach(shape => shape.remove());
                    canvas.occupiedSpaces = [];
                });
                
                // Remove all additional canvases except the first one
                const canvasContainer = document.getElementById("canvasContainer");
                while (canvasContainer.children.length > 1) {
                    canvasContainer.removeChild(canvasContainer.lastChild);
                }
                
                canvases = [{ element: document.getElementById("canvas"), occupiedSpaces: [] }];
                currentCanvasIndex = 0;
                
                showToast("All shapes have been cleared");
            }
        }
        
        function showToast(message) {
            const toast = document.getElementById("toast");
            toast.textContent = message;
            toast.classList.add("show");
            
            setTimeout(() => {
                toast.classList.remove("show");
            }, 3000);
        }
    </script>
   <script>
    let selectedShape = null;

    // Make shapes selectable
    document.addEventListener('click', function (e) {
        const isShape = e.target.classList.contains('shape');
        
        // Remove selection from any previously selected shape
        if (selectedShape) {
            selectedShape.classList.remove('selected');
            selectedShape = null;
        }

        // If clicked on a shape, select it
        if (isShape) {
            selectedShape = e.target;
            selectedShape.classList.add('selected');
        }
    });

    // Listen for Backspace to remove selected shape
    document.addEventListener('keydown', function (e) {
        if ((e.key === 'Backspace' || e.key === 'Delete') && selectedShape) {
            e.preventDefault(); // Prevent page navigation

            // Remove from DOM
            selectedShape.remove();

            // Remove from occupiedSpaces
            canvases.forEach(canvas => {
                canvas.occupiedSpaces = canvas.occupiedSpaces.filter(space => {
                    const x = parseInt(selectedShape.style.left);
                    const y = parseInt(selectedShape.style.top);
                    const width = parseInt(selectedShape.style.width) || selectedShape.offsetWidth;
                    const height = parseInt(selectedShape.style.height) || selectedShape.offsetHeight;
                    return !(space.x === x && space.y === y && space.width === width && space.height === height);
                });
            });

            showToast("Shape removed");
            selectedShape = null;
        }
    });
    
</script>

