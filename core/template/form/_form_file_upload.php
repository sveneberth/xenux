<label for="{{name}}" class="file-upload-button">{{label}}</label>
<input type="file" name="{{name}}#if(multiple):[]#endif" id="{{name}}" #if(multiple):multiple#endif {# style="display:none;{{style}}" #}>
