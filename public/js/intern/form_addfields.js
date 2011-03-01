// http://www.jeremykendall.net/2009/01/19/dynamically-adding-elements-to-zend-form/

var newfield_url = "/ajax/newfield/format/html/medium/";

$(document).ready(function() {

    $("#addElement").click( 
            function() { 
                var id = $("#id").val();
                ajaxAddField(id);
            }
        );
    
    $("#removeElement").click(
            function() {
                var id = $("#id").val();
                removeField(id);
            }
        );
    }
);

// Retrieve new element's html from controller
function ajaxAddField(id) {
    $.ajax(
        {
            type: "POST",
            url: newfield_url + id,
            data: "id=" + id,
            cache: false,
            success: function(newElement) {
                
                // Stop after 10 inserts
                if (11 > id) {
                    // Insert new element before the Add button
                    $("#addElement-label").before(newElement);
                    
                    // Parse the element to receive Dojo style
                    var n = dojo.byId("newType" + id);
                    n.outerHTML = newElement;
                    dojo.parser.parse(n);
                    
                    // Increment and store id
                    $("#id").val(++id);
                }
            }
        }
    );
}

function removeField(id) {
    
    // Get the last used id
    var lastId = $("#id").val() - 1;
    
    // Counter cannot be negative
    if (0 < lastId) {
        
        var node = "newType" + lastId;
        
        // Destroy dijits before destroying the node
        var n = dojo.byId(node);
    	var dijits = dijit.findWidgets(n);
    	if (dijits) {
    		  for (var i = 0, n = dijits.length; i < n; i++) {
    		      dijits[i].destroyRecursive();
    		  }
		}
    	dijits = dojo.parser.parse(node);

        // Destroy the node
        dojo.destroy(node);
        
        // Decrement and store id
        $("#id").val(--id);
    }
}