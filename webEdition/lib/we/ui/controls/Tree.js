/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile 
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */


/**
 * Class for handling we_ui_controls_Tree Element
 * 
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

function we_ui_controls_Tree(treeId) 
{
	
	/**
	 * id of the element
	 *
	 * @var object
	 */
	this.id = treeId;


	/**
	 * root node of the tree 
	 *
	 */
	this.rootNode = eval("tree_"+this.id+".getRoot();");
	
		
	/**
	 * adds a node
	 *
	 * @param id integer
	 * @param text string
	 * @param contentType string
	 * @param parentId 
	 * @param published 
	 * @param status 
	 * 
	 * @return parentId integer
	 * 
	 */
	 
	this.addNode = function(id, text, contentType, parentId, published, status) {
	
		text = text.replace(/</g,"&lt;");
		text = text.replace(/>/g,"&gt;");
		if(parentId>0) {
			var mParentNode = eval("tree_"+this.id+".getNodeByProperty('id',parentId);");
		}   
		else {
			var mParentNode = this.rootNode;
		}
		var classA= new Array();
		classA.push('selected');
		if(published==0) {classA.push('unpublished');}
		var classStr = classA.join(" ");
		classStr = classStr.replace (/^\s+/, '').replace (/\s+$/, '');
		if (classStr!=''){classStr = 'class="'+classStr+'"';}else classStr='';
		if((mParentNode.childrenRendered && mParentNode!="RootNode") || mParentNode=="RootNode") {
			var myobj = { 
					label: "<span title=\""+id+"\" "+classStr+" id=\"spanText_"+this.id+"_"+id+"\">"+text+"</span>",
					id: id,
					text: text,
					title: id		
			};
			
			var childNode = new YAHOO.widget.TextNode(myobj, mParentNode, false);
			if(contentType!="folder") {
				childNode.isLeaf = true;	
			}
			childNode.labelStyle = contentType;
			
			eval("tree_"+this.id+"_activEl = childNode.data.id");
	
			eval("tree_"+this.id+".draw();"); 
			
		}
	}
	
	/**
	 * moves a node
	 *
	 * @param id integer
	 * @param newParentId integer
	 */
	this.moveNode = function(id, newParentId) {

		var mNode = eval("tree_"+this.id+".getNodeByProperty('id',id);");	
						
		eval("tree_"+this.id+".popNode(mNode);");
		
		if(newParentId==0) {
			mNode.appendTo(this.rootNode);
		}
		else {
			var mParentNode = eval("tree_"+this.id+".getNodeByProperty('id',newParentId);");
			if(mParentNode.childrenRendered) {
				mNode.appendTo(mParentNode);
			}
		}
				
		eval("tree_"+this.id+".draw();");      
		
	}

	/**
	 * marks a node
	 *
	 * @param id integer
	 * @param mark boolean
	 */
	this.markNode = function(id, mark) {

		var mNodeSpan = document.getElementById('spanText_'+this.id+'_'+id+'');

		if(mNodeSpan) {
			if(mark) {
				classA = mNodeSpan.className.split(" ");
				classA.push("selected");
				mNodeSpan.className = classA.join(" ");
				mNodeSpan.className = mNodeSpan.className.replace (/^\s+/, '').replace (/\s+$/, '');
			}
			else {				
				mNodeSpan.className = mNodeSpan.className.replace(/selected/,"");
				mNodeSpan.className = mNodeSpan.className.replace (/^\s+/, '').replace (/\s+$/, '');
			}
		}
	}
	
	/**
	 * marks a node as published/unpublished
	 *
	 * @param id integer
	 * @param status boolean
	 */
	this.markNodeStatus = function(id, status) {
		var mNodeSpan = document.getElementById('spanText_'+this.id+'_'+id+'');
		if(mNodeSpan) {
			classB= new Array();
			classA = mNodeSpan.className.split(" ");
			for (var i=0; i<classA.length; i++){
				if(classA[i] == 'selected' || classA[i] == 'unpublished'){
					classB.push(classA[i]);	
				}
			}
			if(status != '') {
				classB.push(status);
			}
			mNodeSpan.className = classB.join(" ");
			mNodeSpan.className= mNodeSpan.className.replace (/^\s+/, '').replace (/\s+$/, '');
		}
	}
	
	/**
	 * marks a node as published/unpublished
	 *
	 * @param id integer
	 * @param mark boolean
	 */
	this.markNodeP = function(id, mark) {
		var mNodeSpan = document.getElementById('spanText_'+this.id+'_'+id+'');
		if(mNodeSpan) {
			if(!mark) {
				classA = mNodeSpan.className.split(" ");
				classA.push("unpublished");
				mNodeSpan.className = classA.join(" ");
				mNodeSpan.className= mNodeSpan.className.replace (/^\s+/, '').replace (/\s+$/, '');
			} else {				
				mNodeSpan.className = mNodeSpan.className.replace(/unpublished/,"");
				mNodeSpan.className = mNodeSpan.className.replace (/^\s+/, '').replace (/\s+$/, '');				
			}
		}
	}
	
	/**
	 * unmark all nodes
	 *
	 */
	this.unmarkAllNodes = function() {

		var nodes = eval("tree_"+this.id+"._nodes");
		for (var i in nodes) {
            var n = nodes[i];
            this.markNode(n.data.id, false);
        }
	}
	
	/**
	 * renames a node
	 *
	 * @param id integer
	 * @param text string
	 */
	this.renameNode = function(id, text) {
		
		text = text.replace(/</g,"&lt;");
		text = text.replace(/>/g,"&gt;");
		var mNode = eval("tree_"+this.id+".getNodeByProperty('id',id);");
		mNode.label = "<span title=\""+id+"\" id=\"spanText_"+this.id+"_"+id+"\">"+text+"</span>";
		mNode.text = text;
		var mNodeSpan = document.getElementById('spanText_'+this.id+'_'+id+'');
		mNodeSpan.innerHTML = text;
		
	}

	/**
	 * removes a node
	 *
	 * @param id integer
	 */
	this.removeNode = function(id) {

		var mNode = eval("tree_"+this.id+".getNodeByProperty('id',id);");
		
		eval("tree_"+this.id+".removeNode(mNode);");
		
		eval("tree_"+this.id+".draw();");  

	}
	
	/**
	 * return the parentId of a node
	 *
	 * @param id integer
	 */
	this.getParentId = function(id) {

		var mNode = eval("tree_"+this.id+".getNodeByProperty('id',id);");   
		
		var parentNode = mNode.parent;    
		
		var parentId = 0;
		if(parentNode.data) {
			parentId = parentNode.data.id;
		}
		
		return parentId;
		
	}
	
	
	/**
	 * return the label of a node
	 *
	 * @param id integer
	 */
	this.getLabel = function(id) {

		var mNode = eval("tree_"+this.id+".getNodeByProperty('id',id);");   

		return mNode.data.text;      
		
	}
	
	/**
	 * return the status of a node
	 *
	 * @param id integer
	 */
	this.getStatus = function(id) {

		var mNode = eval("tree_"+this.id+".getNodeByProperty('id',id);");   

		return mNode.data.Status;      
		
	}
	
	/**
	 * return the published status of a node
	 *
	 * @param id integer
	 */
	this.getPublished = function(id) {

		var mNode = eval("tree_"+this.id+".getNodeByProperty('id',id);");   

		return mNode.data.Published;      
		
	}
	
	/**
	 * check if id exists in tree
	 *
	 * @param id integer
	 * return boolean
	 */
	this.idExists = function(id) {

		var mNode = eval("tree_"+this.id+".getNodeByProperty('id',id);");   

		if(mNode==null) {
			return false;
		}   
		return true; 
		
	}
	
	/**
	 * check if label exists in tree
	 *
	 * @param label 
	 * return boolean
	 */
	this.labelExists = function(label) {

		var mNode = eval("tree_"+this.id+".getNodeByProperty('text',label);");   

		if(mNode==null) {
			return false;
		}   
		return true;      
		
	}
}

