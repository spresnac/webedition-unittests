/**
 * webEdition CMS
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

	function multi_editMulti(parentId,form,itemNum,but,width,editable,minCount) {

		this.variantCount = 0;
		this.itemCount = 0;
		this.currentVariant = 0;
		this.button = "";
		this.minCount = 2;
		this.imageIDText ="";
		this.successorIDText ="";
		this.ImagesHidden =0;
		this.MediaHidden =0;
		this.SuccessorsHidden =0;
		this.defWidth = width;
		this.name = "me" + Math.round(Math.random()*10000);
		this.parent = document.getElementById(parentId);
		this.form = form;
		this.editable = editable;
		this.delRelatedItems = false;
		this.SelectImageCmd = '';

		this.SetMinCount = function(minCount){
			this.minCount =minCount;
		}
		this.toggleMinCount = function(){
			if (this.minCount ==1) {this.minCount == 2;} else {this.minCount == 1;} 
		}
		this.SetImageIDText = function(derText){
			this.imageIDText =derText;
		}
		this.SetMediaIDText = function(derText){
			this.mediaIDText =derText;
		}
		this.SetSuccessorIDText = function(derText){
			this.successorIDText =derText;
		}
		this.toggleImages = function(){
			if(this.ImagesHidden) { 
				this.ImagesHidden = 0;
				for(var i=0;i<this.itemCount;i++){
					document.getElementById('tabrowImageID'+i).style.display = "block";
				}
			} else {
				this.ImagesHidden = 1;
				for(var i=0;i<this.itemCount;i++){
					document.getElementById('tabrowImageID'+i).style.display = "none";
				}

			}
			
		}
		this.hideImages = function(){
			this.ImagesHidden = 1;
			for(var i=0;i<this.itemCount;i++){
				document.getElementById('tabrowImageID'+i).style.display = "none";
			}
		}
		this.showImages = function(){
			this.ImagesHidden = 0;
			for(var i=0;i<this.itemCount;i++){
				document.getElementById('tabrowImageID'+i).style.display = "block";
			}
		}
		this.toggleMedia = function(){
			if(this.MediaHidden) { 
				this.MediaHidden = 0;
				for(var i=0;i<this.itemCount;i++){
					document.getElementById('tabrowMediaID'+i).style.display = "block";
				}
			} else {
				this.MediaHidden = 1;
				for(var i=0;i<this.itemCount;i++){
					document.getElementById('tabrowMediaID'+i).style.display = "none";
				}

			}
			
		}
		this.hideMedia = function(){
			this.MediaHidden = 1;
			for(var i=0;i<this.itemCount;i++){
				document.getElementById('tabrowMediaID'+i).style.display = "none";
			}
		}
		this.showMedia = function(){
			this.MediaHidden = 0;
			for(var i=0;i<this.itemCount;i++){
				document.getElementById('tabrowMediaID'+i).style.display = "block";
			}
		}
		this.toggleSuccessors = function(){
			if(this.SuccessorsHidden) { 
				this.SuccessorsHidden = 0;
				for(var i=0;i<this.itemCount;i++){
					document.getElementById('tabrowSuccessorID'+i).style.display = "block";
				}
			} else {
				this.SuccessorsHidden = 1;
				for(var i=0;i<this.itemCount;i++){
					document.getElementById('tabrowSuccessorID'+i).style.display = "none";
				}

			}
			
		}
		this.hideSuccessors = function(){
			this.SuccessorsHidden = 1;
			for(var i=0;i<this.itemCount;i++){
				document.getElementById('tabrowSuccessorID'+i).style.display = "none";
			}
		}
		this.showSuccessors = function(){
			this.SuccessorsHidden = 0;
			for(var i=0;i<this.itemCount;i++){
				document.getElementById('tabrowSuccessorID'+i).style.display = "block";
			}
		}
		this.createItemHidden = function (name){

			var item = document.createElement("input");
			item.setAttribute("name",name);
			item.setAttribute("id",name);
			item.setAttribute("type","hidden");
			form.appendChild(item);

			//this.form.appendChild(item);
			this.parent.appendChild(item);

			item = null;
		}

		this.updateHidden = function(item,value){
			this.form.elements[this.name+"_variant"+this.currentVariant+"_"+this.name+"_"+item].value=value; 
			
		}
		
		

		this.addVariant = function (){
			for(var i=0;i<this.itemCount;i++){
				this.createItemHidden(this.name+"_variant"+this.variantCount+"_"+this.name+"_item"+i);
				this.createItemHidden(this.name+"_variant"+this.variantCount+"_"+this.name+"_itemImageID"+i);
				this.createItemHidden(this.name+"_variant"+this.variantCount+"_"+this.name+"_itemMediaID"+i);
				this.createItemHidden(this.name+"_variant"+this.variantCount+"_"+this.name+"_itemSuccessorID"+i);
			}
			this.variantCount++;
		}

		this.deleteVariant = function (variant){
			if(variant<(this.variantCount-1)){
				for(var i=variant+1;i<this.variantCount;i++){
					for(var j=0;j<this.itemCount;j++){
						this.form.elements[this.name+"_variant"+(i-1)+"_"+this.name+"_item"+j].value = this.form.elements[this.name+"_variant"+i+"_"+this.name+"_item"+j].value;
					}
				}
			}

			this.variantCount--;
			for(var z=0;z<this.itemCount;z++){
				var item = document.getElementById(this.name+"_variant"+this.variantCount+"_"+this.name+"_item"+z);
				
				//this.form.removeChild(item);
				this.parent.removeChild(item);
				var item = document.getElementById(this.name+"_variant"+this.variantCount+"_"+this.name+"_itemImageID"+z);
				var item = document.getElementById(this.name+"_variant"+this.variantCount+"_"+this.name+"_itemMediaID"+z);
				var item = document.getElementById(this.name+"_variant"+this.variantCount+"_"+this.name+"_itemSuccessorID"+z);
				this.parent.removeChild(item);
			}
			if(variant<(this.variantCount-1)) this.currentVariant=variant;
			else this.currentVariant=this.variantCount-1;

			this.showVariant(this.currentVariant);

		}

		this.addItem = function (){

			

			var butt=this.button.replace("#####placeHolder#####",this.name+".delItem("+this.itemCount+")");

			var set = document.createElement("div");
			set.setAttribute("id",this.name+"_item"+this.itemCount);

			if(this.editable == true){
				TabStart = "<table style=\"margin-bottom:5px;\" cellpadding=0 cellspacing=0 border=0><tr valign=\"middle\"><td style=\"width:"+this.defWidth+"\"><input name=\""+this.name+"_item"+this.itemCount+"\" id=\""+this.name+"_item_input_"+this.itemCount+"\" type=\"text\" style=\"width:"+this.defWidth+"\" onkeyup=\""+this.name+".updateHidden(\'item"+this.itemCount+"\',this.value)\" class=\"wetextinput\"></td><td>&nbsp;</td><td>" + butt + "</td></tr>";
				TabEnd =  "</table>";
				
				TabMiddle1 = "<tr valign=\"middle\" id=\"tabrowImageID"+this.itemCount+"\"><td style=\"width:"+this.defWidth+"\"><table style=\"margin-bottom:0px;\" cellpadding=0 cellspacing=0 border=0><tr valign=\"middle\"><td style=\"width:120\" class=\"defaultfont\">"+this.imageIDText+"</td><td><input name=\""+this.name+"_itemImageID"+this.itemCount+"\" id=\""+this.name+"_itemImageID_input_"+this.itemCount+"\" type=\"text\" style=\"width:383\" onkeyup=\""+this.name+".updateHidden(\'itemImageID"+this.itemCount+"\',this.value)\" class=\"wetextinput\"></td></tr></table></td><td>&nbsp;</td><td>&nbsp;</td></tr>";
				TabMiddle2 = "<tr valign=\"middle\" id=\"tabrowMediaID"+this.itemCount+"\"><td style=\"width:"+this.defWidth+"\"><table style=\"margin-bottom:0px;\" cellpadding=0 cellspacing=0 border=0><tr valign=\"middle\"><td style=\"width:120\" class=\"defaultfont\">"+this.mediaIDText+"</td><td><input name=\""+this.name+"_itemMediaID"+this.itemCount+"\" id=\""+this.name+"_itemMediaID_input_"+this.itemCount+"\" type=\"text\" style=\"width:383\" onkeyup=\""+this.name+".updateHidden(\'itemMediaID"+this.itemCount+"\',this.value)\" class=\"wetextinput\"></td></tr></table></td><td>&nbsp;</td><td>&nbsp;</td></tr>";

				TabMiddle3 = "<tr valign=\"middle\" id=\"tabrowSuccessorID"+this.itemCount+"\"><td style=\"width:"+this.defWidth+"\"><table style=\"margin-bottom:0px;\" cellpadding=0 cellspacing=0 border=0><tr valign=\"middle\"><td style=\"width:120\" class=\"defaultfont\">"+this.successorIDText+"</td><td><input name=\""+this.name+"_itemSuccessorID"+this.itemCount+"\" id=\""+this.name+"_itemSuccessorID_input_"+this.itemCount+"\" type=\"text\" style=\"width:383\" onkeyup=\""+this.name+".updateHidden(\'itemSuccessorID"+this.itemCount+"\',this.value)\" class=\"wetextinput\"></td></tr></table></td><td>&nbsp;</td><td>&nbsp;</td></tr>";

				set.innerHTML = TabStart+TabMiddle1+TabMiddle2+TabMiddle3+TabEnd; 
			}
			else{
				set.innerHTML = "<table style=\"margin-bottom:5px;\" cellpadding=0 cellspacing=0 border=0><tr valign=\"middle\"><td style=\"width:"+this.defWidth+"\"><label id=\""+this.name+"_item_label_"+this.itemCount+"\" class=\"defaultfont\"></td><td>&nbsp;</td><td>" + butt + "</td></tr></table>";
			}

			this.parent.appendChild(set);

			set = null;

			for(var j=0;j<this.variantCount;j++){
				this.createItemHidden(this.name+"_variant"+j+"_"+this.name+"_item"+this.itemCount);
				this.createItemHidden(this.name+"_variant"+j+"_"+this.name+"_itemImageID"+this.itemCount);
				this.createItemHidden(this.name+"_variant"+j+"_"+this.name+"_itemSuccessorID"+this.itemCount);			
			}

			this.itemCount++;
			if (this.ImagesHidden) {this.hideImages();}else {this.showImages();}
			if (this.MediaHidden) {this.hideMedia();}else {this.showMedia();}
			if (this.SuccessorsHidden) {this.hideSuccessors();}else {this.showSuccessors();}
		}

		this.delItem = function(child){
			this.itemCount--;
			for(var i=0;i<this.variantCount;i++){
				if(child<this.itemCount){
					for(var j=child+1;j<(this.itemCount+1);j++){
						this.form.elements[this.name+"_variant"+i+"_"+this.name+"_item"+(j-1)].value = this.form.elements[this.name+"_variant"+i+"_"+this.name+"_item"+j].value;
					}
				}
				var item = document.getElementById(this.name+"_variant"+i+"_"+this.name+"_item"+this.itemCount);
				//this.form.removeChild(item);
				this.parent.removeChild(item);
			}

			var item1 = document.getElementById(this.name+"_item"+this.itemCount);
			this.parent.removeChild(item1);
			if(this.delRelatedItems) {
				document.getElementById("updateScores").value=true;
				elemRow = document.getElementById("row_scores_"+child);
				elemRow.parentNode.removeChild(elemRow);
				var xcount=child+1;
				while(elemRow = document.getElementById("row_scores_"+xcount)){
				 	elemRow.setAttribute('id',"row_scores_"+(xcount-1));
				 	var elemX;
				 	if(elemX=document.getElementById("scores_"+xcount)) {
				 		elemX.setAttribute('id',"scores_"+(xcount-1));
				 		elemX.setAttribute('name',"scores_"+(xcount-1));
				 	}
				 	xcount++;
				}
			}
			this.showVariant(this.currentVariant);
		}
		
		this.setItem = function (variant,item,value){//alert(this.name+"_variant"+variant+"_"+this.name+"_item"+item);
			this.form.elements[this.name+"_variant"+variant+"_"+this.name+"_item"+item].value=value;
		}
		this.setItemImageID = function (variant,item,value){
			this.form.elements[this.name+"_variant"+variant+"_"+this.name+"_itemImageID"+item].value=value;
			//alert(this.name+"_variant"+variant+"_"+this.name+"_itemImageID"+item+ ' ' +this.form.elements[this.name+"_variant"+variant+"_"+this.name+"_itemImageID"+item].value);
		}
		this.setItemMediaID = function (variant,item,value){
			this.form.elements[this.name+"_variant"+variant+"_"+this.name+"_itemMediaID"+item].value=value;
			
		}
		this.setItemSuccessorID = function (variant,item,value){
			this.form.elements[this.name+"_variant"+variant+"_"+this.name+"_itemSuccessorID"+item].value=value;
			//alert(this.name+"_variant"+variant+"_"+this.name+"_itemSuccessorID"+item+ ' ' +this.form.elements[this.name+"_variant"+variant+"_"+this.name+"_itemSuccessorID"+item].value);
		}


		this.setRelatedItems = function (item) {
			this.relatedItems[this.itemCount] = item; 
		}
		
		this.showVariant = function (variant){
			for(var i=0;i<this.itemCount;i++){
				if(variant!=this.currentVariant && this.editable) {
					this.setItem(this.currentVariant,i,this.form.elements[this.name+"_item"+i].value);
				}
				if( typeof(this.form.elements[this.name+"_variant"+variant+"_"+this.name+"_item"+i])!='undefined'){
					if(this.editable) {
						this.form.elements[this.name+"_item"+i].value=this.form.elements[this.name+"_variant"+variant+"_"+this.name+"_item"+i].value;
						this.form.elements[this.name+"_itemImageID"+i].value=this.form.elements[this.name+"_variant"+variant+"_"+this.name+"_itemImageID"+i].value;
						this.form.elements[this.name+"_itemMediaID"+i].value=this.form.elements[this.name+"_variant"+variant+"_"+this.name+"_itemMediaID"+i].value;
						this.form.elements[this.name+"_itemSuccessorID"+i].value=this.form.elements[this.name+"_variant"+variant+"_"+this.name+"_itemSuccessorID"+i].value;
						
					}
					else {
						var item = document.getElementById(this.name+"_item_label_"+i);
						item.innerHTML = this.form.elements[this.name+"_variant"+variant+"_"+this.name+"_item"+i].value;
					}
				}
			}
			this.currentVariant=variant;
		}

		this.button = but;
		for(i=0;i<itemNum;i++){
			this.addItem();
		}

		eval(this.name + "=this");
	}