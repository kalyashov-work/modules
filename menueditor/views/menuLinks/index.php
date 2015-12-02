<?php 
   
?>

<div class="row-fluid">
    <div class="span12">
        <div class="portlet box red">
            <div class="portlet-title">
                <div class="caption"><i class="icon-cogs"></i>SEDMAX | Редактор меню</div>
                <div class="actions">
                    <a id="change_search" href="#" class="btn red toggle"><i id="search_icon" class="fa fa-sort"></i>По номерам</a>
                    <a href="<?php
                       #$url_add = Yii::app()->controller->module->id.'/'.Yii::app()->controller->id.'/create';
                        $url_add = "/menueditor/menulinks/create";
                        echo Yii::app()->createUrl($url_add); ?>" class="btn red"><i class="fa fa-plus"></i> Добавить пункт</a>
                    <!--<a id="save_structure" href="#" class="btn red toggle"><i class="fa fa-save"></i> Сохранить структуру</a>-->
                </div>
            </div>

            <div class="portlet-body">
                <input type="search" size="40" id="search" class="form-control" style="margin-bottom:20px;">
                
                <div id="objects_tree" class="tree-demo">
                </div>
                <div class="alert alert-info no-margin margin-top-10">
                    Редактирование пунктов меню осуществляется через контекстное меню (правый клик мышью)
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() 
{
    var tree;

   /**
    *  Ф-я для получения структуры деревва
    */
    function getTreeStructure()
    {

        var json = $.jstree.reference('#objects_tree').get_json(null, {"flat": true, "no_state": true,}); 
        var jsonStr = JSON.stringify(json);
        var jsonData = JSON.parse(jsonStr);

        //все узлы с детьми
        var nodes = [];
        var childIndex = -1;
        var index = 0;

        for(var i = 0; i < jsonData.length; i++)
        {
            if(jsonData[i].parent === "#")
            {   
                nodes[index] = {
                    id: jsonData[i].id,
                    text: jsonData[i].text,
                    parent: jsonData[i].parent,
                    order: jsonData[i].data.order,
                    childs: []
                }

                index++;
                childIndex++;
            }
            else
            {
                var children = {
                    id: jsonData[i].id,
                    text: jsonData[i].text,
                    parent: jsonData[i].parent,
                    order: jsonData[i].data.order,
                }
                        
                nodes[childIndex].childs.push(children);
            }
        }
        return nodes;
    }


   /**
    * Ф-я для получения индекса элемента в структуре дерева
    */
    function getIndexNodeInTree(nodes, nodeId)
    {
        for(var index = 0; index < nodes.length; index++)
        {
            if(nodes[index].id === nodeId)
            {
                return index;
            }
        }
    }

   /**
    * Ф-я для генерации последовательности
    *
    */
    function* generateSequence(start, end) 
    {

        for (var i = start; i <= end; i++) 
        {
            yield i;
        }
    }

   /** 
    * Ф-я для проверки массива на уникальность
    *
    
    function isUnigueArray(a) 
    {     
        return a.sort().filter(function(item, pos, ary) 
        {
            return !pos || item != ary[pos - 1];     
        }) 
    }*/


   /** 
    * Ф-я для проверки массива на уникальность
    *
    */ 
    function isUniqueArray(array)
    {   
        var n = array.length;
        for (var i = 0; i < n-1; i++)
        { 
            for (var j = i+1; j < n; j++)
            { 
                if (array[i] != array[j]) 
                    return [...generateSequence(1,array.length)];
            }
        }

        return array;
    }




    function arraysEqual(a1,a2) 
    {
        return JSON.stringify(a1)==JSON.stringify(a2);
    }

    function orderChanges(nodes,index,parentId)
    {
        if(isNaN(parentId))             ///!!!!!!!!!!!!
        {
            var newOrder=[];
            for(var i = 0; i < nodes.length; i++)
            {
                newOrder[i] = {
                    id: nodes[i].id,
                    text: nodes[i].text,
                    order: nodes[i].order,
                }
            }
            return newOrder;

        }
        else
        {
            var newOrder = [];
            for(var i = 0; i < nodes[index].childs.length; i++)
            {
                newOrder[i] = {
                    id: nodes[index].childs[i].id,
                    text: nodes[index].childs[i].text,
                    order: nodes[index].childs[i].order,
                }
            }
            return newOrder;
        }
    }

    

    var search = 0;
    $('#change_search').click(function() 
    {
        if(search)
        {
            search = 0;
            $(this).text("По номерам");

        }
        else
        {
            search = 1;
            $(this).text("По алфавиту");
        }

        $('#objects_tree').jstree('refresh');
    });

    $("#objects_tree")
        .on('changed.jstree', function (e, data) 
        {
            tree = getTreeStructure();
        })
        .bind("before.jstree", function (e, data)
        { 

        })
        .bind("delete_node.jstree", function(e, data) 
        {
            

        })
        .on('rename_node.jstree', function (e, data) 
        {
            if(data.text != data.old)
            {
                $.ajax({
                    type: "POST",
                    cache: false,
                    url: '/menueditor/menulinks/rename/id/'+(data.node.id) + '/newTitle/'+ data.text,
                        dataType: "html",
                        success: function (res) {
                            toastr.success('Пункт меню переименован');
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            toastr.error('Ошибка при переименовании пункта меню');
                        },
                        async: false
                    });
            }
            else
            {
                toastr.error('Пункт меню не переименован');
            }
            
            
        })
        .on('move_node.jstree', function (e, data) 
        {

            var parent = 0;
            if(data.parent != '#')
                parent = data.parent;

            $.ajax({
                type: "POST",
                cache: false,
                  url: '/menueditor/menulinks/changeParent/id/'+(data.node.id) + '/newId/'+ parent,
                    dataType: "html",
                    success: function (res) {
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                    },
                    async: false
                });      
                
                
                var nodes = getTreeStructure();
            
                var index = getIndexNodeInTree(nodes, data.node.parent);

                var nodesToChange = orderChanges(nodes, index, data.node.parent);
     
                
                console.debug(nodesToChange);

                var newOrder = []; 

                for(var item in nodesToChange)
                {
                    if(Number.isInteger(nodesToChange[item].order))
                        newOrder.push(nodesToChange[item].order);
                }

                function compareNumeric(a, b) {
                    return a - b;
                }

                newOrder.sort(compareNumeric);
                newOrder = isUniqueArray(newOrder);

                for(var item in newOrder)
                {
                    nodesToChange[item].order = newOrder[item];
                }

            
                var nodesJson = JSON.stringify(nodesToChange);
                console.debug(nodesJson);

                $.ajax({
                type: "POST",
                cache: false,
                url: '/menueditor/menulinks/changeOrder/',
                data: 'data=' + nodesJson,
                dataType: "html",
                    success: function (res) {
                        toastr.success('Пункт меню перемещен');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        toastr.error('Ошибка');
                    },
                    async: false
                });  

            localStorage['lastMovedNodeParent'] = data.node.parent;
        })
        .jstree(
        {
            "core" : 
            {
                "themes" : 
                {
                    "responsive": false,
                    "dots": false,
                },
                
                // so that create works
                //"check_callback" : true,
                "check_callback" : function (op, node, par, pos, more) 
                {

                    if (op === "move_node") 
                    {
                        
                    }

                    if(op === "rename_node")
                    {
                        
                    }

                    if(op === "delete_node") 
                    {

                        if (node.children.length > 0)
                        {
                            alert('Невозможно удалить пункт меню с дочерними пунктами');
                            return false;
                        }

                        if (confirm("Вы уверены в удалении пункта меню?"))
                        {
                            $.ajax(
                            {
                                type: "POST",
                                cache: false,
                                url: '/menueditor/menulinks/delete/id/'+node.id,
                                dataType: "html",
                                async: false,
                                success: function (res) {

                                    toastr.success('Пункт меню удален');
                                    return true;
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    toastr.error('Ошибка при удалении пункта меню');
                                    return false;
                                }

                            });
                        }
                        else
                        {
                            return false;
                        }

                    }

                    return true;
                },
                'data' : {
                    'url' : function (node) {
                        return '/menueditor/generatetree/generate/';
                    },
                    'data' : function (node) {
                        return { 'id' : node.id };
                    }
                }
            },
            "dnd" : 
            {
                    "drop_finish" : function () {
                        alert("DROP");
                    },
                    "drag_check" : function (data) {
                        if(data.r.attr("id") == "phtml_1") {
                            return false;
                        }
                        return {
                            after : false,
                            before : false,
                            inside : true
                        };

                        
                    },
                    "drag_finish" : function () {
                        
                    },
                    "check_while_dragging": true
            },
            
            "contextmenu":{
                    "items": function () {
                        return {
                            "Create": {
                                "label": "Создать",
                                "action": function (data) 
                                {
                                    var json = $.jstree.reference('#objects_tree').get_json(null, { "flat": true, "no_state": true,}); 
                                    alert(JSON.stringify(json));
                                
                                }
                            },
                            "Edit": 
                            {
                                "label": "Редактировать",
                                "action": function (data) 
                                {
                                    var inst = $.jstree.reference(data.reference);
                                        obj = inst.get_node(data.reference);
                                     window.location.href = "/menueditor/menulinks/update/id/" + obj.id;
                                }
                            },
                            "Rename": 
                            {
                                "label": "Переименовать",
                                "action": function (data) 
                                {
                                    var inst = $.jstree.reference(data.reference);
                                        obj = inst.get_node(data.reference);
                                    inst.edit(obj);
                                }
                            },
                            "Delete": {
                                "label": "Удалить",
                                "action": function (data) {
                                    var ref = $.jstree.reference(data.reference),
                                        sel = ref.get_selected();
                                    if(!sel.length) { return false; }
                                    ref.delete_node(sel);

                                }
                            }
                        };
                    }
                },
            
            "types" : {
                "default" : {
                    "icon" : "fa fa-folder icon-warning icon-lg",
                    "max_depth" : 1,

                },
                "root" : {
                    "icon" : "fa fa-circle icon-warning icon-lg",
                    "max_depth" : 1,
                },
                "file" : {
                    "icon" : "fa fa-file icon-warning icon-lg"
                }
            },
            "state" : { "key" : "main_objects_tree" },
            "plugins" : [ "contextmenu", "dnd", "state", "types", "search","sort"],
            "sort": function (a, b) 
            {
                if(search)
                    return this.get_text(a) > this.get_text(b) ? 1 : -1;
            },
        });

        var to = false;
        $('#search').keyup(function () 
        {
            if(to) 
            { 
                clearTimeout(to); 
            }
            to = setTimeout(function () 
            {
                var v = $('#search').val();
                $('#objects_tree').jstree(true).search(v);
            }, 250);
        });

    });
   
</script>