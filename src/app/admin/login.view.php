<v:head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no" />
	<js:style path="~/css/login.css" />
	<bs:resources />
</v:head>

<v:title value="is4wfw" />
<login:init group="web-admins" />

<bs:container class="is4wfw">
    <bs:row vertical="center" class="justify-content-center">
        <bs:column small="8" medium="6" large="4" >
            <bs:card header="&lt;is4wfw /&gt;" class="shadow">
                <edit:form submit="login">
                    <web:condition when="edit:save">
                        <login:login group="web-admins" username="edit:username" password="edit:password">
                            <web:condition when="var:adminLastPage" isInverted="true">
                                <var:declare name="adminLastPage" value="~/in/index.view" />
                            </web:condition>
                            <web:redirectTo pageId="var:adminLastPage" />
                        </login:login>
                    </web:condition>

                    <web:condition when="post:login">
                        <p class="text-danger font-weight-bold">
                            Wrong combination of username and password.
                        </p>
                    </web:condition>
                    
                    <bs:formGroup label="Username:">
                        <ui:textbox name="username" class="form-control form-control-sm" autofocus="autofocus" />
                    </bs:formGroup>
                    <bs:formGroup label="Password:">
                        <ui:passwordbox name="password" type="password" class="form-control form-control-sm" />
                    </bs:formGroup>
                    
                    <button name="login" value="login" class="btn btn-secondary mt-2 px-3">
                        Login
                    </button>
                </edit:form>
            </bs:card>
        </bs:column>
    </bs:row>
</bs:container>